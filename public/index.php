<?php

use Infrastructure\ISession;


$sessionPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0700, true);
}
ini_set('session.save_path', $sessionPath);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);


$componentsPath = __DIR__ . "../../src/Presentation/Components";
include '../src/app.php';

$session = $container->get(ISession::class);
$session->start();
$isAuthenticated = $session->has('userData');


?>
<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="/assets/style.css" />
</head>

<body>
    <?php require_once $componentsPath . "/Header.php" ?>

    <main class="main" id="root"></main>

    <?php require_once $componentsPath . "/Footer.php"  ?>
</body>


<script>
    window.authState = {
        isAuthenticated: <?php echo json_encode($isAuthenticated); ?>,
        userId: <?php echo json_encode($_SESSION['userData']['id'] ?? null); ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        const burgerBtn = document.getElementById("burgerBtn");
        const mobileMenu = document.getElementById("mobileMenu");
        const menuOverlay = document.getElementById("menuOverlay");

        console.log(burgerBtn)

        if (burgerBtn && mobileMenu && menuOverlay) {
            function toggleMenu() {
                burgerBtn.classList.toggle("active");
                mobileMenu.classList.toggle("active");
                menuOverlay.classList.toggle("active");
            }

            burgerBtn.addEventListener("click", toggleMenu);
            menuOverlay.addEventListener("click", toggleMenu);

            document.querySelectorAll(".menu-item").forEach((item) => {
                item.addEventListener("click", () => {
                    toggleMenu();
                });
            });

            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && mobileMenu.classList.contains("active")) {
                    toggleMenu();
                }
            });
        }
    });
</script>

<script>
    window.addEventListener("DOMContentLoaded", () => {
        const didMount = (callback, deps) => {
            let isDependenciesChanged = false;

            for (let i = 0; i < deps.length; i += 1) {
                if (dependencies[i] !== previousDependencies[i]) {
                    isDependenciesChanged = true;
                }
            }

            previousDependencies = deps;

            if (isDependenciesChanged) {
                callback();
            }
        }

        new(class FetchSPA {
            constructor() {
                this.routes = {
                    "/registration": {
                        title: "Регистрация",
                        endpoint: "/registration",
                        protected: false
                    },
                    "/": {
                        title: "Вход",
                        endpoint: "/",
                        protected: false
                    },
                    "/profile/:id": {
                        title: "Профиль",
                        endpoint: "/profile/:id",
                        protected: true
                    },
                    "/sessions/:id": {
                        title: "Сессии",
                        endpoint: "/sessions/:id",
                        protected: true
                    },
                    "/logout:id": {
                        title: "Выход",
                        endpoint: "/",
                        protected: false
                    },
                };
                this.redirectAfterLogin = null;

                this.contentElement = document.getElementById("root");
                this.currentController = null;
                this.init();
            }

            init() {
                document.addEventListener("click", (e) => {
                    if (e.target.matches("[data-route]")) {
                        e.preventDefault();
                        const route = e.target.getAttribute("data-route");
                        this.navigateTo(route);
                    }
                });

                window.addEventListener("popstate", () => {
                    if (!window.authState.isAuthenticated) {
                        this.loadRoute('/');
                        return
                    }
                    this.loadRoute(window.location.pathname);
                });

                this.renderHeader();
                this.renderOverlay();
                this.loadRoute(window.location.pathname);
            }

            navigateTo(route) {
                history.pushState({}, "", route);

                const routeData = this.findRoute(route);

                if (!window.authState.isAuthenticated && routeData['route']['protected']) {
                    this.loadRoute('/');
                    return
                }
                this.loadRoute(route);
            }

            // Новый метод для поиска подходящего маршрута
            findRoute(path) {
                if (this.routes[path]) {
                    return {
                        route: this.routes[path],
                        endpoint: this.routes[path].endpoint
                    };
                }

                // Ищем маршруты с параметрами
                for (const [routePath, routeData] of Object.entries(this.routes)) {
                    if (routePath.includes(':')) {
                        const routeMatch = this.matchRoute(routePath, path);
                        if (routeMatch.matches) {
                            let endpoint = routeData.endpoint;
                            for (const [param, value] of Object.entries(routeMatch.params)) {
                                endpoint = endpoint.replace(`:${param}`, value);
                            }
                            return {
                                route: routeData,
                                endpoint: endpoint
                            };
                        }
                    }
                }

                // Маршрут не найден
                return null;
            }

            // Метод для сопоставления маршрута с параметрами
            matchRoute(routePath, actualPath) {
                const routeParts = routePath.split('/');
                const actualParts = actualPath.split('/');

                if (routeParts.length !== actualParts.length) {
                    return {
                        matches: false,
                        params: {}
                    };
                }

                const params = {};
                for (let i = 0; i < routeParts.length; i++) {
                    if (routeParts[i].startsWith(':')) {
                        // Это параметр
                        const paramName = routeParts[i].substring(1);
                        params[paramName] = actualParts[i];
                    } else if (routeParts[i] !== actualParts[i]) {
                        // Части не совпадают
                        return {
                            matches: false,
                            params: {}
                        };
                    }
                }

                return {
                    matches: true,
                    params: params
                };
            }

            async loadRoute(route) {
                this.showLoading();
                this.updateActiveLink(route);

                const routeData = this.findRoute(route);

                if (routeData['route']['protected'] && !window.authState.isAuthenticated) {
                    this.redirectAfterLogin = route;
                    this.navigateTo("/");
                    return;
                }


                if (!routeData) {
                    this.showError('Маршрут не найден');
                    return;
                }

                didMount(this.renderHeader(), window.authState.isAuthenticated)
                didMount(this.renderOverlay(), window.authState.isAuthenticated)

                try {
                    const html = await this.fetchHTML(routeData.endpoint);
                    this.contentElement.innerHTML = html;
                    this.handleFormSubmit(routeData.endpoint);
                } catch (err) {
                    console.error('Error loading route:', err);
                    this.showError(err.message);
                }
            }

            async fetchHTML(endpoint) {
                if (this.currentController) {
                    this.currentController.abort();
                }

                this.currentController = new AbortController();
                const signal = this.currentController.signal;

                try {
                    const res = await fetch(endpoint, {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "xmlhttprequest",
                            "Content-Type": "text/html",
                        },
                        signal,
                    });

                    if (!res.ok) {
                        throw new Error(`Ошибка ${res.status}: ${res.statusText}`);
                    }

                    const text = await res.text();
                    return text;
                } catch (err) {
                    console.error('Fetch error:', err);
                    throw err;
                }
            }

            async handleFormSubmit(endpoint) {
                const form = document.getElementById("formDataForm");

                if (form) {
                    form.onsubmit = async (e) => {
                        e.preventDefault();
                        const formData = new FormData(form);

                        try {
                            const res = await fetch(endpoint, {
                                method: "POST",
                                body: formData,
                                headers: {
                                    "X-Requested-With": "xmlhttprequest",
                                },
                            });

                            const contentType = res.headers.get("content-type");

                            if (contentType && contentType.includes("application/json")) {
                                const jsonResponse = await res.json();

                                if (res.ok) {
                                    if (jsonResponse.success) {
                                        this.showSuccess(
                                            jsonResponse.message || "Операция выполнена успешно"
                                        );
                                        setTimeout(() => {
                                            this.setAuth(true);
                                            window.authState.userId = jsonResponse.data;

                                            if (this.redirectAfterLogin) {
                                                this.navigateTo(`${this.redirectAfterLogin}`);
                                            } else {
                                                this.navigateTo(`/profile/${jsonResponse.data}`);
                                            }

                                        }, 1500);
                                    } else {
                                        this.showError(jsonResponse.message || "Произошла ошибка");
                                    }
                                } else {
                                    this.showError(jsonResponse.message || `Ошибка ${res.status}`);
                                }
                            } else {
                                if (res.ok) {
                                    const html = await res.text();
                                    this.contentElement.innerHTML = html;
                                } else {
                                    throw new Error(`Ошибка ${res.status}: ${res.statusText}`);
                                }
                            }
                        } catch (err) {
                            this.showError(err.message);
                        }
                    };
                }
            }

            showLoading() {
                this.contentElement.innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        Загружаем данные через fetch...
                    </div>`;
            }

            showError(message) {
                console.log(message)
                this.contentElement.innerHTML = `
                    <div class="error">
                        <h2>Ошибка</h2>
                        <p>${message}</p>
                    </div>`;
            }

            showSuccess(message) {
                this.contentElement.innerHTML = `
                    <div class="success">
                    <h2>Успешно!</h2>
                    <p>${message}</p>
                    </div>`;
            }

            renderNav(containerId) {
                const container = document.getElementById(containerId);
                if (!container) return;

                if (window.authState.isAuthenticated) {
                    const userId = window.authState.userId;
                    container.innerHTML = `
                        <p><a href="/profile/${userId}" data-route="/profile/${userId}" class="header_profile-link">Профиль</a></p>
                        <p><a href="/sessions/${userId}" data-route="/sessions/${userId}" class="header_profile-link">Мои Сессии</a></p>
                        <p><a href="/logout" id="logoutBtn" class="header_profile-link">Выйти</a></p>
                    `;
                    document.getElementById("logoutBtn").addEventListener("click", (e) => {

                        e.preventDefault();
                        const res = fetch(`/logout`, {
                            method: "DELETE",
                            headers: {
                                "X-Requested-With": "xmlhttprequest",
                            },
                        }).then(() => {
                            this.setAuth(false);
                            window.authState.userId = null;
                            this.navigateTo("/");
                        })

                    });
                } else {
                    container.innerHTML = `
                        <p><a href="/registration" data-route="/registration" class="header_profile-link">Зарегистрироваться</a></p>
                        <p><a href="/" data-route="/" class="header_profile-link">Войти</a></p>
                    `;
                }
            }

            renderHeader() {
                this.renderNav("headerProfile")
            }

            renderOverlay() {
                this.renderNav("mobileMenu")
            }

            setAuth(isAuth) {
                window.authState.isAuthenticated = isAuth;
                this.renderHeader();
                this.renderOverlay();
            }

            updateActiveLink(route) {
                document.querySelectorAll(".nav-link").forEach((link) => {
                    link.classList.remove("active");
                });

                let activeLink = document.querySelector(`[data-route="${route}"]`);

                if (!activeLink) {
                    const routeData = this.findRoute(route);
                    if (routeData) {
                        for (const [routePath] of Object.entries(this.routes)) {
                            if (this.matchRoute(routePath, route).matches) {
                                activeLink = document.querySelector(`[data-route="${routePath}"]`);
                                break;
                            }
                        }
                    }
                }

                if (activeLink) {
                    activeLink.classList.add("active");
                }
            }
        })();
    });
</script>

</html>

</html>