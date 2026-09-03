<?php

namespace App\Support\Menu;

use Illuminate\Http\Request;

class MenuActiveMatcher
{
    public function __construct(
        protected Request $request,
    ) {}

    public function currentPath(): string
    {
        return $this->normalizePath($this->request->path());
    }

    public function normalizePath(?string $path): string
    {
        $path = parse_url((string) $path, PHP_URL_PATH) ?? (string) $path;
        $path = '/'.trim($path, '/');

        if ($path === '/') {
            return '/';
        }

        return rtrim($path, '/') ?: '/';
    }

    /**
     * @param  array<int, string|null>  $peerRoutes
     */
    public function isRouteActive(?string $route, array $peerRoutes = []): bool
    {
        if ($route === null || trim($route) === '' || str_starts_with(trim($route), 'javascript:')) {
            return false;
        }

        $current = $this->currentPath();

        foreach ($this->equivalentPaths($route) as $target) {
            if ($this->pathMatches($target, $current, $peerRoutes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string|null>  $peerRoutes
     */
    public function itemClass(?string $route, array $peerRoutes = []): string
    {
        return $this->isRouteActive($route, $peerRoutes) ? 'active' : '';
    }

    /**
     * @param  iterable<int, string|null>  $routes
     */
    public function groupClass(iterable $routes): string
    {
        $routeList = collect($routes)
            ->filter(fn ($route) => filled($route))
            ->values()
            ->all();

        foreach ($routeList as $route) {
            if ($this->isRouteActive($route, $routeList)) {
                return 'active open';
            }
        }

        return '';
    }

    /**
     * @return array<int, string>
     */
    public function collectGroupRoutes(object $menuGroup): array
    {
        $routes = [];

        foreach ($menuGroup->menuDetails ?? [] as $menuDetail) {
            if (filled($menuDetail->route ?? null)) {
                $routes[] = $menuDetail->route;
            }

            foreach ($menuDetail->subMenuDetails ?? [] as $subMenuDetail) {
                if (filled($subMenuDetail->route ?? null)) {
                    $routes[] = $subMenuDetail->route;
                }
            }
        }

        return array_values(array_unique($routes));
    }

    /**
     * @return array<int, string>
     */
    protected function equivalentPaths(string $path): array
    {
        $path = $this->normalizePath($path);
        $paths = [$path];

        if (str_starts_with($path, '/frontend/')) {
            $paths[] = '/internal/'.substr($path, strlen('/frontend/'));
        } elseif (str_starts_with($path, '/internal/')) {
            $paths[] = '/frontend/'.substr($path, strlen('/internal/'));
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  array<int, string|null>  $peerRoutes
     */
    protected function pathMatches(string $target, string $current, array $peerRoutes): bool
    {
        if ($current === $target) {
            return true;
        }

        if (! str_starts_with($current, $target.'/')) {
            return false;
        }

        $peerTargets = collect($peerRoutes)
            ->filter(fn ($route) => filled($route))
            ->flatMap(fn (string $route) => $this->equivalentPaths($route))
            ->unique()
            ->filter(fn (string $peer) => $peer !== $target && str_starts_with($peer, $target.'/'));

        foreach ($peerTargets as $peer) {
            if ($current === $peer || str_starts_with($current, $peer.'/')) {
                return false;
            }
        }

        return true;
    }
}
