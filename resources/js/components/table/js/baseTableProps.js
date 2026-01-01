// A single place for BaseTable's prop defs (keeps <script setup> tiny)
export const baseTableProps = {
    columns: { type: Array, required: true },

    total: { type: Number, default: 0 },
    loading: { type: Boolean, default: false },

    page: { type: Number, default: 1 },
    pageSize: { type: Number, default: 10 },
    pageSizeOptions: { type: Array, default: () => [5, 10, 20, 50] },
    pageSizeLabel: { type: String, default: "Per page" },

    search: { type: String, default: "" },
    searchPlaceholder: { type: String, default: "Search…" },

    defaultSort: { type: Object, default: undefined }, // { by, dir }
    enableSearch: { type: Boolean, default: true },
    enableSort: { type: Boolean, default: true },

    hidePager: { type: Boolean, default: false },
    dense: { type: Boolean, default: false },
    stickyHeader: { type: Boolean, default: true },

    routeSync: { type: Boolean, default: false },
    routeNs: { type: String, default: "" },
    routeHistory: { type: Boolean, default: false },

    autoload: { type: Boolean, default: true },

    filters: { type: Array, default: () => [] }, 
};
