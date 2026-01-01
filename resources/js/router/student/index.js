const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

export default [
    {
        path: "/mcqs",
        component: AuthenticatedLayout,
        children: [
            {
                path: "",
                name: "StudentMCQ",
                component: () => import("@/pages/student/MCQPractice.vue"),
                meta: {
                    requiresAuth: true,
                    title: "MCQ Practice",
                },
            },
        ],
    },
];
