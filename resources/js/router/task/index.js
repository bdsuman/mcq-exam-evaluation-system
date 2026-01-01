const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");
import AdminMiddleware from "@/middleware/AdminMiddleware";

export default [
    {
        path: "/tasks",
        component: AuthenticatedLayout,
        beforeEnter: AdminMiddleware,
        children: [
            {
                path: "",
                name: "TaskIndex",
                component: () => import("@/pages/tasks/Index.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Task Management",
                },
            },
            {
                path: "create",
                name: "TaskCreate",
                component: () =>
                    import("@/pages/tasks/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Create Task" },
            },
            {
                path: "update/:id",
                name: "TaskUpdate",
                component: () =>
                    import("@/pages/tasks/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Update Task" },
            },
            {
                path: ":id",
                name: "TaskDetails",
                component: () => import("@/pages/tasks/Details.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Task Details",
                },
            },
        ],
    },
];
