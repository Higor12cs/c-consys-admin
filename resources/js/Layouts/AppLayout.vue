<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import {
    Users,
    Menu,
    House,
    TrendingUpDown,
    Calendar,
    ChevronDown,
    Circle,
} from "lucide-vue-next";
import { Link, usePage } from "@inertiajs/vue3";

const sidebarToggled = ref(false);
const indicatorsExpanded = ref(false);
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
        route: "indicators",
        icon: TrendingUpDown,
        label: "Indicadores",
        submenu: [
            {
                route: "indicators.index",
                label: "Indicadores",
            },
            {
                route: "schedules.index",
                label: "Agendamentos",
            },
            {
                route: "images.index",
                label: "Imagens",
            },
        ],
    },
    {
        route: "users.index",
        icon: Users,
        label: "Usuários",
    },
];

const isActiveLink = (routeName) => {
    const routePrefix = routeName.split(".")[0];
    const componentParts = page.component.split("/");
    const lastPart =
        componentParts[componentParts.length - 2]?.toLowerCase() ||
        componentParts[0].toLowerCase();

    return lastPart === routePrefix.toLowerCase();
};

const hasActiveSubmenu = (submenu) => {
    return submenu?.some((sublink) => isActiveLink(sublink.route));
};

navLinks.forEach((link) => {
    if (link.submenu && hasActiveSubmenu(link.submenu)) {
        indicatorsExpanded.value = true;
    }
});

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
            <Link :href="route('home.index')" class="sidebar-brand gap-2">
                <img
                    src="/public/images/ConsysLogo.png"
                    alt="Consys Logo"
                    style="height: 24px;"
                />
                <span>Consys</span>
            </Link>

            <nav class="sidebar-nav">
                <template v-for="link in navLinks" :key="link.route">
                    <template v-if="link.submenu">
                        <button
                            class="nav-link"
                            :class="{
                                active: hasActiveSubmenu(link.submenu),
                            }"
                            @click="indicatorsExpanded = !indicatorsExpanded"
                        >
                            <component :is="link.icon" :size="18" />
                            <span>{{ link.label }}</span>
                            <ChevronDown
                                :size="16"
                                class="submenu-arrow"
                                :class="{ expanded: indicatorsExpanded }"
                            />
                        </button>
                        <div class="submenu" v-show="indicatorsExpanded">
                            <Link
                                v-for="sublink in link.submenu"
                                :key="sublink.route"
                                class="nav-link submenu-link"
                                :class="{ active: isActiveLink(sublink.route) }"
                                :href="route(sublink.route)"
                            >
                                <Circle :size="18" class="submenu-icon" />
                                <span>{{ sublink.label }}</span>
                            </Link>
                        </div>
                    </template>

                    <Link
                        v-else
                        class="nav-link"
                        :class="{ active: isActiveLink(link.route) }"
                        :href="route(link.route)"
                    >
                        <component :is="link.icon" :size="18" />
                        <span>{{ link.label }}</span>
                    </Link>
                </template>
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
                                <span class="user-name">
                                    {{ page.props.auth.user.name }}
                                </span>
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
