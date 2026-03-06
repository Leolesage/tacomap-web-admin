<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\ApiClient;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Validator;

final class TacosPlaceController
{
    private array $config;
    private ApiClient $apiClient;
    private int $limit;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->apiClient = new ApiClient($config['api'] ?? []);
        $this->limit = (int)($config['admin']['list_limit'] ?? 10);
    }

    public function home(Request $request): void
    {
        if (Auth::check()) {
            Response::redirect('/admin/tacos-places');
        }
        Response::redirect('/tacos-places');
    }

    public function publicIndex(Request $request): void
    {
        $q = trim((string)$request->queryParam('q', ''));
        $page = max(1, (int)$request->queryParam('page', 1));
        $this->renderIndexPage($page, $q, false);
    }

    public function publicSearch(Request $request): void
    {
        $q = trim((string)$request->queryParam('q', ''));
        $page = max(1, (int)$request->queryParam('page', 1));
        $this->renderSearchResults($page, $q, false);
    }

    public function publicShow(Request $request): void
    {
        $id = (int)$request->param('id');
        $this->renderShowPage($id, false);
    }

    public function index(Request $request): void
    {
        $q = trim((string)$request->queryParam('q', ''));
        $page = max(1, (int)$request->queryParam('page', 1));
        $this->renderIndexPage($page, $q, true);
    }

    public function search(Request $request): void
    {
        $q = trim((string)$request->queryParam('q', ''));
        $page = max(1, (int)$request->queryParam('page', 1));
        $this->renderSearchResults($page, $q, true);
    }

    public function create(Request $request): void
    {
        $defaults = $this->defaultFormData();
        $html = View::render('tacos_places/create', [
            'title' => 'Create Tacos Place',
            'errors' => [],
            'old' => $defaults,
        ]);
        Response::html($html);
    }

    public function store(Request $request): void
    {
        $data = $request->post;
        $files = $request->files;

        [$errors, $clean] = Validator::validateTacosPlace($data, $files, true);
        if (!empty($errors)) {
            $old = $this->mergeOld($data);
            $html = View::render('tacos_places/create', [
                'title' => 'Create Tacos Place',
                'errors' => $errors,
                'old' => $old,
            ]);
            Response::html($html, 422);
        }

        $res = $this->apiClient->createTacosPlace(Auth::token(), $clean, $files['photo']);
        if (!$res['ok']) {
            $errors = $this->apiErrors($res);
            $old = $this->mergeOld($data);
            $html = View::render('tacos_places/create', [
                'title' => 'Create Tacos Place',
                'errors' => $errors,
                'old' => $old,
            ]);
            Response::html($html, 422);
        }

        $created = $res['data'] ?? [];
        $id = (int)($created['id'] ?? 0);
        View::setFlash('flash_success', 'TacosPlace créé.');
        Response::redirect('/admin/tacos-places/' . $id);
    }

    public function show(Request $request): void
    {
        $id = (int)$request->param('id');
        $this->renderShowPage($id, true);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->param('id');
        $res = $this->apiClient->getTacosPlace($id, Auth::token());
        if (!$res['ok']) {
            Response::abort(404, 'TacosPlace not found.');
        }

        $item = $res['data'] ?? [];
        $old = $this->mergeOld($item);
        $html = View::render('tacos_places/edit', [
            'title' => 'Edit TacosPlace',
            'errors' => [],
            'old' => $old,
            'item' => $item,
        ]);
        Response::html($html);
    }

    public function update(Request $request): void
    {
        $id = (int)$request->param('id');
        $existingRes = $this->apiClient->getTacosPlace($id, Auth::token());
        if (!$existingRes['ok']) {
            Response::abort(404, 'TacosPlace not found.');
        }
        $existing = $existingRes['data'] ?? [];

        $data = $request->post;
        $files = $request->files;

        $merged = array_merge($existing, $data);
        [$errors, $clean] = Validator::validateTacosPlace($merged, $files, false);
        if (!empty($errors)) {
            $old = $this->mergeOld($merged);
            $html = View::render('tacos_places/edit', [
                'title' => 'Edit TacosPlace',
                'errors' => $errors,
                'old' => $old,
                'item' => $existing,
            ]);
            Response::html($html, 422);
        }

        $photo = null;
        if (!empty($files['photo']) && ($files['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo = $files['photo'];
        }

        $res = $this->apiClient->updateTacosPlace(Auth::token(), $id, $clean, $photo);
        if (!$res['ok']) {
            $errors = $this->apiErrors($res);
            $old = $this->mergeOld($merged);
            $html = View::render('tacos_places/edit', [
                'title' => 'Edit TacosPlace',
                'errors' => $errors,
                'old' => $old,
                'item' => $existing,
            ]);
            Response::html($html, 422);
        }

        View::setFlash('flash_success', 'TacosPlace modifie.');
        Response::redirect('/admin/tacos-places/' . $id);
    }

    public function delete(Request $request): void
    {
        $id = (int)$request->param('id');
        $res = $this->apiClient->deleteTacosPlace(Auth::token(), $id);
        if (!$res['ok']) {
            Response::abort(404, 'TacosPlace not found.');
        }

        View::setFlash('flash_success', 'TacosPlace supprimé.');
        Response::redirect('/admin/tacos-places');
    }

    public function pdf(Request $request): void
    {
        $id = (int)$request->param('id');
        $res = $this->apiClient->downloadTacosPlacePdf(Auth::token(), $id);
        if (!$res['ok']) {
            Response::abort(404, 'PDF unavailable.');
        }

        $headers = $res['headers'] ?? [];
        $contentType = (string)($headers['content-type'] ?? 'application/pdf');
        $disposition = (string)($headers['content-disposition'] ?? ('attachment; filename="tacos-place-' . $id . '.pdf"'));

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: ' . $disposition);
        echo $res['body'] ?? '';
        exit;
    }

    private function defaultFormData(): array
    {
        return [
            'name' => '',
            'description' => '',
            'date' => '',
            'price' => '',
            'latitude' => '48.8566',
            'longitude' => '2.3522',
            'contact_name' => '',
            'contact_email' => '',
        ];
    }

    private function mergeOld(array $data): array
    {
        $data['date'] = $this->toInputDate((string)($data['date'] ?? ''));
        return $data;
    }

    private function toInputDate(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        try {
            $dt = new \DateTime($value);
            return $dt->format('Y-m-d\\TH:i');
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function fetchList(int $page, string $q): array
    {
        $res = $this->apiClient->listTacosPlaces($page, $this->limit, $q, Auth::token());
        if (!$res['ok']) {
            return [
                'ok' => false,
                'error' => (string)($res['error'] ?? 'API error'),
                'items' => [],
                'pagination' => [
                    'page' => $page,
                    'limit' => $this->limit,
                    'hasMore' => false,
                    'total' => 0,
                ],
            ];
        }

        $payload = $res['data'] ?? [];
        return [
            'ok' => true,
            'items' => $payload['data'] ?? [],
            'pagination' => [
                'page' => (int)($payload['page'] ?? $page),
                'limit' => (int)($payload['limit'] ?? $this->limit),
                'hasMore' => (bool)($payload['hasMore'] ?? false),
                'total' => (int)($payload['total'] ?? 0),
            ],
        ];
    }

    private function apiErrors(array $apiResponse): array
    {
        if (!empty($apiResponse['fields']) && is_array($apiResponse['fields'])) {
            $errors = [];
            foreach ($apiResponse['fields'] as $field => $code) {
                $fieldName = (string)$field;
                $errors[$fieldName] = $this->mapApiError($fieldName, (string)$code);
            }
            return $errors;
        }
        return ['form' => (string)($apiResponse['error'] ?? 'Erreur API')];
    }

    private function mapApiError(string $field, string $code): string
    {
        if ($code === 'required') {
            return match ($field) {
                'name' => 'Nom requis.',
                'description' => 'Description requise.',
                'date' => 'Date requise.',
                'price' => 'Prix requis.',
                'latitude' => 'Latitude requise.',
                'longitude' => 'Longitude requise.',
                'contact_name' => 'Nom du contact requis.',
                'contact_email' => 'Email du contact requis.',
                'photo' => 'Photo requise.',
                default => 'Champ requis.',
            };
        }

        if ($code === 'invalid') {
            return match ($field) {
                'date' => 'Date invalide.',
                'price' => 'Prix invalide.',
                'latitude' => 'Latitude invalide.',
                'longitude' => 'Longitude invalide.',
                'contact_email' => 'Email invalide.',
                default => 'Valeur invalide.',
            };
        }

        if ($code === 'max_length') {
            return match ($field) {
                'name' => 'Nom trop long (max 255 caracteres).',
                'description' => 'Description trop longue (max 5000 caracteres).',
                'contact_name' => 'Nom du contact trop long (max 255 caracteres).',
                'contact_email' => 'Email trop long (max 255 caracteres).',
                default => 'Texte trop long.',
            };
        }

        return $code;
    }

    private function renderIndexPage(int $page, string $q, bool $isAdmin): void
    {
        $token = $isAdmin ? Auth::token() : '';
        $result = $this->fetchListWithToken($page, $q, $token);

        if (!$result['ok']) {
            View::setFlash('flash_error', (string)$result['error']);
        }

        $basePath = $isAdmin ? '/admin/tacos-places' : '/tacos-places';
        $title = $isAdmin ? 'Tacos Places Admin' : 'TacoMap France';
        $html = View::render('tacos_places/index', [
            'title' => $title,
            'items' => $result['items'],
            'pagination' => $result['pagination'],
            'q' => $q,
            'isAdmin' => $isAdmin,
            'basePath' => $basePath,
        ]);
        Response::html($html);
    }

    private function renderSearchResults(int $page, string $q, bool $isAdmin): void
    {
        $token = $isAdmin ? Auth::token() : '';
        $result = $this->fetchListWithToken($page, $q, $token);
        $basePath = $isAdmin ? '/admin/tacos-places' : '/tacos-places';

        $html = View::render('tacos_places/_results', [
            'items' => $result['items'],
            'pagination' => $result['pagination'],
            'q' => $q,
            'isAdmin' => $isAdmin,
            'basePath' => $basePath,
        ], false);
        Response::html($html);
    }

    private function renderShowPage(int $id, bool $isAdmin): void
    {
        $token = $isAdmin ? Auth::token() : '';
        $res = $this->apiClient->getTacosPlace($id, $token);
        if (!$res['ok']) {
            Response::abort(404, 'TacosPlace not found.');
        }

        $item = $res['data'] ?? [];
        $basePath = $isAdmin ? '/admin/tacos-places' : '/tacos-places';
        $html = View::render('tacos_places/show', [
            'title' => 'TacosPlace Details',
            'item' => $item,
            'isAdmin' => $isAdmin,
            'basePath' => $basePath,
        ]);
        Response::html($html);
    }

    private function fetchListWithToken(int $page, string $q, string $token): array
    {
        $res = $this->apiClient->listTacosPlaces($page, $this->limit, $q, $token);
        if (!$res['ok']) {
            return [
                'ok' => false,
                'error' => (string)($res['error'] ?? 'API error'),
                'items' => [],
                'pagination' => [
                    'page' => $page,
                    'limit' => $this->limit,
                    'hasMore' => false,
                    'total' => 0,
                ],
            ];
        }

        $payload = $res['data'] ?? [];
        return [
            'ok' => true,
            'items' => $payload['data'] ?? [],
            'pagination' => [
                'page' => (int)($payload['page'] ?? $page),
                'limit' => (int)($payload['limit'] ?? $this->limit),
                'hasMore' => (bool)($payload['hasMore'] ?? false),
                'total' => (int)($payload['total'] ?? 0),
            ],
        ];
    }
}
