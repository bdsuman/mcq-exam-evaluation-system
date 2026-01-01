const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");
import AdminMiddleware from "@/middleware/AdminMiddleware";

export default [
    {
        path: "/questions",
        component: AuthenticatedLayout,
        beforeEnter: AdminMiddleware,
        children: [
            {
                path: "",
                name: "QuestionIndex",
                component: () => import("@/pages/questions/Index.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Question Management",
                },
            },
            {
                path: "create",
                name: "QuestionCreate",
                component: () =>
                    import("@/pages/questions/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Create Question" },
            },
            {
                path: "update/:id",
                name: "QuestionUpdate",
                component: () =>
                    import("@/pages/questions/CreateOrUpdate.vue"),
                meta: { requiresAuth: true, title: "Update Question" },
            },
            {
                path: ":id",
                name: "QuestionDetails",
                component: () => import("@/pages/questions/Details.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Question Details",
                },
            },
        ],
    },
];
