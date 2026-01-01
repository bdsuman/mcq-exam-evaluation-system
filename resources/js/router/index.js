import { createWebHistory, createRouter } from "vue-router";
import { trans, loadLanguageAsync } from "laravel-vue-i18n";
import { useUserStore } from "@/stores/useUserStore";
import task from "./task";

const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

const routes = [
  {
    path: "/",
    component: AuthenticatedLayout,
    children: [
      {
        path: "/",
        name: "home",
        component: () => import("@/pages/tasks/Index.vue"),
        meta: {
          requiresAuth: true,
          title: "Home",
        },
      },
      {
        path: "/profile",
        name: "profile",
        component: () => import("@/pages/auth/Profile.vue"),
        meta: {
          requiresAuth: true,
          title: "Profile",
        },
      },
      {
        path: "/change-password",
        name: "changePassword",
        component: () => import("@/pages/auth/ChangePassword.vue"),
        meta: {
          requiresAuth: true,
          title: "Change Password",
        },
      },
    ],
  },
  ...task,
  {
    path: "/login",
    name: "login",
    component: () => import("@/pages/auth/Login.vue"),
    meta: {
      requiresAuth: false,
      title: "Login",
    },
  },
  {
    path: "/signup",
    name: "signup",
    component: () => import("@/pages/auth/Signup.vue"),
    meta: {
      requiresAuth: false,
      title: "Signup",
    },
  },
  {
    path: "/error",
    name: "Error",
    component: () => import("@/pages/errors/Index.vue"),
    meta: {
      requiresAuth: false,
      title: "Error",
    },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: (to) => ({
      path: "/error",
      query: { code: 404, message: "Not Found", from: to.fullPath },
    }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// for authenticated routes
router.beforeEach(async (to, from) => {
  const store = useUserStore();
  await loadLanguageAsync(store?.language ?? "de");

  // Set page title
  document.title = trans(to.meta.title ?? "Deep Grow");

  // Not authenticated and requires auth → go to login
  if (to.meta.requiresAuth && store.token == 0) {
    return { name: "login" };
  }

  // If already logged in and visiting a public route
  // Exclude error + not-found redirection
  if (
    to.meta.requiresAuth === false &&
    store.token != 0 &&
    to.name !== "Error"
  ) {
    return { name: "home" };
  }
});

export default router;
