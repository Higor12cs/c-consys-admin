<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import {
    LayoutDashboard,
    Users,
    Menu,
    Clock,
    House,
    TrendingUpDown,
    Calendar,
} from "lucide-vue-next";
import { Link, usePage } from "@inertiajs/vue3";

const sidebarToggled = ref(false);
const page = usePage();

const navLinks = [
    {
        route: "home.index",
        icon: House,
        label: "Início",
    },
    {
        route: "customers.index",
        icon: Users,
        label: "Clientes",
    },
    {
        route: "tasks.index",
        icon: Calendar,
        label: "Tarefas",
    },
    {
        route: "indicators.index",
        icon: TrendingUpDown,
        label: "Indicadores",
    },
    {
        route: "users.index",
        icon: Users,
        label: "Usuários",
    },
];

const isActiveLink = (routeName) => {
    return page.component.startsWith(
        routeName.split(".")[0].charAt(0).toUpperCase() +
            routeName.split(".")[0].slice(1)
    );
};

const closeSidebar = (event) => {
    if (window.innerWidth < 768) {
        const sidebar = document.querySelector(".sidebar");
        if (
            sidebar &&
            !sidebar.contains(event.target) &&
            !event.target.closest(".btn-default")
        ) {
            sidebarToggled.value = false;
        }
    }
};

onMounted(() => {
    document.addEventListener("click", closeSidebar);
});

onUnmounted(() => {
    document.removeEventListener("click", closeSidebar);
});
</script>

<template>
    <div id="wrapper">
        <aside class="sidebar" :class="{ toggled: sidebarToggled }">
            <Link :href="route('home.index')" class="sidebar-brand">
                <span>Consys</span>
            </Link>

            <nav class="sidebar-nav">
                <Link
                    v-for="link in navLinks"
                    :key="link.route"
                    class="nav-link"
                    :class="{ active: isActiveLink(link.route) }"
                    :href="route(link.route)"
                >
                    <component :is="link.icon" :size="18" />
                    <span>{{ link.label }}</span>
                </Link>
            </nav>
        </aside>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="topbar">
                    <button
                        @click="sidebarToggled = !sidebarToggled"
                        class="btn btn-default d-md-none"
                    >
                        <Menu :size="20" />
                    </button>

                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="userDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <span class="user-name">{{
                                    page.props.auth.user.name
                                }}</span>
                                <div class="user-avatar">
                                    {{
                                        page.props.auth.user.name
                                            ? page.props.auth.user.name
                                                  .charAt(0)
                                                  .toUpperCase()
                                            : "U"
                                    }}
                                </div>
                            </a>
                            <div
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="userDropdown"
                            >
                                <Link
                                    class="dropdown-item"
                                    :href="route('profile.edit')"
                                >
                                    Perfil
                                </Link>
                                <div class="dropdown-divider"></div>
                                <Link
                                    class="dropdown-item"
                                    :href="route('logout')"
                                    method="post"
                                >
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
