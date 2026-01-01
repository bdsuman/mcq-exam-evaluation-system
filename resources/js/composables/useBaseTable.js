import { computed, reactive, ref, watch, onMounted, nextTick } from "vue";

export function useBaseTable({ props, emit, route, router, tableContainer }) {
    // ------- Columns & first-sortable detection -------
    const normColumns = computed(() =>
        props.columns.map((c) =>
            typeof c === "string"
                ? { key: c, label: c, sortable: true }
                : {
                    key: c.key,
                    label: c.label ?? c.key,
                    sortable: c.sortable ?? true,
                    thClass: c.thClass,
                    width: c.width,
                }
        )
    );
    const firstSortableKey = computed(
        () => normColumns.value?.find((c) => c.sortable !== false)?.key ?? null
    );

    // ------- Local models -------
    const pageLocal = ref(props.page);
    const pageSizeLocal = ref(props.pageSize);
    const searchLocal = ref(props.search || "");
    const sort = reactive({
        by: props.defaultSort?.by ?? firstSortableKey.value,
        dir: props.defaultSort?.dir ?? "desc",
    });

    // Sync down from parent
    watch(() => props.page, (v) => { if (v !== pageLocal.value) pageLocal.value = v; });
    watch(() => props.pageSize, (v) => { if (v !== pageSizeLocal.value) pageSizeLocal.value = v; });
    watch(() => props.search, (v) => { if ((v || "") !== searchLocal.value) searchLocal.value = v || ""; });

    // Scroll to top when page changes
    watch(pageLocal, () => {
        if (tableContainer?.value) tableContainer.value.scrollTo({ top: 0, behavior: "smooth" });
    });

    // Adopt first sortable key if needed
    watch(firstSortableKey, (k) => { if (!sort.by && k) sort.by = k; });

    // ------- Sorting -------
    function toggleSort(colKey) {
        if (!props.enableSort) return;
        if (sort.by === colKey) sort.dir = sort.dir === "asc" ? "desc" : "asc";
        else { sort.by = colKey; sort.dir = "asc"; }
        emit("sort-change", { ...sort });
        request();
    }
    function ariaSort(colKey) {
        if (sort.by !== colKey) return "none";
        return sort.dir === "asc" ? "ascending" : "descending";
    }

    // ------- Pagination -------
    function onPageChange(v) {
        pageLocal.value = v;
        emit("update:page", v);
        request();
    }
    function onPageSizeChange(v) {
        pageSizeLocal.value = v;
        if (pageLocal.value !== 1) { pageLocal.value = 1; emit("update:page", 1); }
        emit("update:pageSize", v);
        request();
    }
    const totalPages = computed(() =>
        props.hidePager ? 1 : Math.max(1, Math.ceil((props.total || 0) / pageSizeLocal.value))
    );
    const rowStart = computed(() => (props.total === 0 ? 0 : (pageLocal.value - 1) * pageSizeLocal.value + 1));
    const rowEnd = computed(() => Math.min(pageLocal.value * pageSizeLocal.value, props.total || 0));

    // ------- Search handlers (debounce happens in TableSearch.vue) -------
    function handleSearchChange(v) {
        searchLocal.value = v ?? "";
        if (pageLocal.value !== 1) { pageLocal.value = 1; emit("update:page", 1); }
        emit("update:search", searchLocal.value);
        request();
    }
    function handleSearchCommit(v) {
        searchLocal.value = v ?? "";
        if (pageLocal.value !== 1) { pageLocal.value = 1; emit("update:page", 1); }
        emit("update:search", searchLocal.value);
        request();
    }

    // ------- Filters -------
    function normalizeToArray(val) {
        return Array.isArray(val) ? val : val == null ? [] : [val];
    }

    // prevent echo when URL sync writes into filters
    let syncingFromRoute = false;

    function onFilterUpdate(f, val) {
        if (syncingFromRoute) return; // ignore route-originated updates

        const next = normalizeToArray(val);
        if (Array.isArray(f.model)) {
            f.model.splice(0, f.model.length, ...next);
        } else {
            f.model = next[0] ?? null;
        }
        if (pageLocal.value !== 1) { pageLocal.value = 1; emit("update:page", 1); }
        request();
    }

    function resetAllFilters() {
        (props.filters || []).forEach((f) => {
            if (Array.isArray(f.model)) f.model.splice(0);
            else f.model = null;
        });
        emit("reset-filters");
        if (pageLocal.value !== 1) { pageLocal.value = 1; emit("update:page", 1); }
        request();
    }

    // ------- Route sync helpers -------
    function qKey(name) {
        return props.routeNs ? `${props.routeNs}.${name}` : name;
    }
    function getQueryValue(q, key, fallback = "") {
        const v = q[qKey(key)];
        return typeof v === "string" ? v : fallback;
    }
    function getIntQueryValue(q, key, fallback) {
        const raw = q[qKey(key)];
        const n = parseInt(raw, 10);
        return Number.isFinite(n) && n > 0 ? n : fallback;
    }
    function normalizeSortDir(v) { return v === "desc" ? "desc" : "asc"; }

    function getArrayQueryValue(q, key, fallback = []) {
        const raw = q[qKey(key)];
        if (raw == null || raw === "") return [];
        const arr = Array.isArray(raw) ? raw : (typeof raw === "string" ? raw.split(",").filter(Boolean) : fallback);
        return [...arr].sort(); // normalize order so comparisons are stable
    }

    // ------- Single-fire emission helpers -------
    let lastPayloadKey = "";
    let requestScheduled = false;

    function buildPayload() {
        return {
            page: pageLocal.value,
            pageSize: pageSizeLocal.value,
            sort: { ...sort },
            search: searchLocal.value,
            filters: Array.isArray(props.filters)
                ? props.filters.reduce((acc, f) => { acc[f.key] = f.model; return acc; }, {})
                : {},
        };
    }

    function emitRequestOnce(payload) {
        const key = JSON.stringify(payload);
        if (key === lastPayloadKey) return; // identical state -> skip
        lastPayloadKey = key;
        emit("request", payload);
    }

    const defaultParams = {
        page: props.page ?? 1,
        pageSize: props.pageSize ?? 10,
        sortBy: props.defaultSort?.by ?? firstSortableKey.value ?? "",
        sortDir: props.defaultSort?.dir ?? "asc",
        search: "",
        ...(Array.isArray(props.filters)
            ? props.filters.reduce((acc, f) => {
                acc[f.key] = ""; // empty default for all filters
                return acc;
            }, {})
            : {}),
    };

    function writeQuery({ page, pageSize, sort, search, filters }) {
        const next = {
            [qKey("page")]: String(page ?? defaultParams.page),
            [qKey("pageSize")]: String(pageSize ?? defaultParams.pageSize),
            [qKey("sortBy")]: sort.by ?? defaultParams.sortBy,
            [qKey("sortDir")]: sort.dir ?? defaultParams.sortDir,
            [qKey("search")]: search ?? "",
        };

        if (Array.isArray(props.filters)) {
            for (const f of props.filters) {
                if (!f?.key) continue;
                const val = Array.isArray(f.model)
                    ? [...f.model].sort().join(",")
                    : f.model ?? "";
                next[qKey(f.key)] = val;
            }
        }

        // Always ensure defaults exist, even if user removed them
        for (const [k, v] of Object.entries(defaultParams)) {
            if (!(qKey(k) in next)) next[qKey(k)] = String(v);
        }

        const nav = props.routeHistory ? router.push : router.replace;
        nav({ query: { ...route.query, ...next } });
        return true;
    }

    // ------- Unified request (single source of truth) -------
    function request() {
        if (requestScheduled) return;
        requestScheduled = true;

        nextTick(() => {
            requestScheduled = false;

            const payload = buildPayload();

            if (props.routeSync) {
                const changed = writeQuery(payload);
                if (!changed) {
                    // URL already matches -> emit directly so user action always fetches
                    emitRequestOnce(payload);
                }
                // If it changed, the route watcher below will emit once.
            } else {
                emitRequestOnce(payload);
            }
        });
    }

    // ------- Route watcher (emit-only; never writes URL; handles back/forward) -------
    if (props.routeSync) {
        let hasBootstrapped = false;

        watch(
            () => route.query,
            (q) => {
                const merged = { ...defaultParams, ...q }; // enforce missing params

                // sync local state from merged instead of raw q
                pageLocal.value = getIntQueryValue(merged, "page", defaultParams.page);
                pageSizeLocal.value = getIntQueryValue(merged, "pageSize", defaultParams.pageSize);
                sort.by = getQueryValue(merged, "sortBy", defaultParams.sortBy);
                sort.dir = normalizeSortDir(getQueryValue(merged, "sortDir", defaultParams.sortDir));
                searchLocal.value = getQueryValue(merged, "search", defaultParams.search);

                if (Array.isArray(props.filters)) {
                    syncingFromRoute = true;
                    for (const f of props.filters) {
                        const vals = getArrayQueryValue(merged, f.key, []);
                        if (Array.isArray(f.model)) f.model.splice(0, f.model.length, ...vals);
                        else f.model = vals.length ? vals[0] : null;
                    }
                    syncingFromRoute = false;
                }

                emitRequestOnce(buildPayload());

                // If user removed params, push back full query
                if (Object.keys(q).length < Object.keys(defaultParams).length) {
                    writeQuery(buildPayload());
                }
            },
            { immediate: true }
        );
    }

    // ------- Initial request for non-routeSync or empty URL -------
    onMounted(async () => {
        if (!props.autoload) return;

        if (!props.routeSync) {
            request();
            return;
        }
        await nextTick();
        if (!route?.query || Object.keys(route.query).length === 0) {
            if (!sort.by && firstSortableKey.value) sort.by = firstSortableKey.value;
            if (!sort.dir) sort.dir = "desc";
            // For routeSync with empty URL, emit once to load
            emitRequestOnce(buildPayload());
        }
    });

    return {
        // state
        normColumns, firstSortableKey,
        pageLocal, pageSizeLocal, searchLocal,
        sort, totalPages, rowStart, rowEnd,

        // actions
        toggleSort, ariaSort,
        onPageChange, onPageSizeChange,
        handleSearchChange, handleSearchCommit,
        onFilterUpdate, resetAllFilters,
        request,
    };
}

