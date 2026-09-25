<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import {
    ArrowUpRight,
    BarChart3,
    ChevronLeft,
    ChevronRight,
    Link2,
    LogOut,
    Plus,
    Search,
    ShieldCheck,
    Archive,
    RefreshCw,
} from "@lucide/vue";
import AuthScreen from "./components/AuthScreen.vue";
import TrafficChart from "./components/TrafficChart.vue";
import LinkEditor from "./components/LinkEditor.vue";
import LinkList from "./components/LinkList.vue";
import { api } from "./lib/api";
const user = ref(null),
    initial = ref(true),
    registration = ref(false),
    error = ref(""),
    notice = ref("");
const links = ref([]),
    stats = ref(null),
    loading = ref(false),
    page = ref(1),
    lastPage = ref(1),
    total = ref(0),
    search = ref(""),
    status = ref(""),
    view = ref("links");
const editing = ref(undefined),
    busyId = ref(null),
    accountBusy = ref(false);
const initials = computed(() =>
    user.value?.name
        .split(" ")
        .map((n) => n[0])
        .slice(0, 2)
        .join(""),
);
let requestController, searchTimer, noticeTimer;
function announce(text) {
    notice.value = text;
    clearTimeout(noticeTimer);
    noticeTimer = setTimeout(() => (notice.value = ""), 6000);
}
async function boot() {
    initial.value = true;
    error.value = "";
    try {
        const s = await api("/session");
        user.value = s.user;
        registration.value = s.registration_enabled;
        if (user.value) await refresh();
    } catch (e) {
        error.value = e.message;
    } finally {
        initial.value = false;
    }
}
async function fetchLinks() {
    requestController?.abort();
    requestController = new AbortController();
    const current = requestController;
    loading.value = true;
    error.value = "";
    try {
        const q = new URLSearchParams({
            page: page.value,
            search: search.value,
            status: status.value,
        });
        const result = await api(`/links?${q}`, { signal: current.signal });
        links.value = result.data;
        lastPage.value = result.meta.last_page;
        total.value = result.meta.total;
    } catch (e) {
        if (e.name !== "AbortError") error.value = e.message;
    } finally {
        if (current === requestController) loading.value = false;
    }
}
async function refresh() {
    await Promise.all([
        fetchLinks(),
        api("/stats")
            .then((data) => (stats.value = data))
            .catch((e) => (error.value = e.message)),
    ]);
}
async function signedIn(value) {
    user.value = value;
    page.value = 1;
    await refresh();
}
async function logout() {
    accountBusy.value = true;
    try {
        await api("/logout", { method: "POST" });
        requestController?.abort();
        user.value = null;
        stats.value = null;
        links.value = [];
        search.value = "";
        status.value = "";
        view.value = "links";
    } catch (e) {
        error.value = e.message;
    } finally {
        accountBusy.value = false;
    }
}
function changeView(value) {
    view.value = value;
    status.value = value === "archived" ? "archived" : "";
    page.value = 1;
    fetchLinks();
}
async function openEditor(link) {
    error.value = "";
    try {
        editing.value = link ? (await api(`/links/${link.id}`)).data : null;
    } catch (e) {
        error.value = e.message;
    }
}
async function saved() {
    const created = editing.value === null;
    editing.value = undefined;
    page.value = 1;
    await refresh();
    announce(
        created
            ? "Your new link is ready to share."
            : "Changes saved. Your short address stays the same.",
    );
}
async function changeStatus(link, next) {
    busyId.value = link.id;
    error.value = "";
    try {
        await api(`/links/${link.id}/status`, {
            method: "PATCH",
            body: { status: next, version: link.version },
        });
        await refresh();
        if (!links.value.length && page.value > 1) {
            page.value--;
            await fetchLinks();
        }
        announce(
            next === "archived"
                ? "Link archived. Restore it anytime from Archive."
                : next === "paused"
                  ? "Link paused. Visitors will see an unavailable page."
                  : "Link restored. Its expiration date still applies.",
        );
    } catch (e) {
        error.value = e.message;
    } finally {
        busyId.value = null;
    }
}
async function copy(link) {
    try {
        await navigator.clipboard.writeText(link.short_url);
        announce("Short link copied. Ready when you are.");
    } catch {
        announce(
            "Clipboard is unavailable. Select and copy the short address.",
        );
    }
}
function changePage(value) {
    page.value = value;
    fetchLinks();
}
watch(search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        if (user.value) {
            page.value = 1;
            fetchLinks();
        }
    }, 250);
});
function filter() {
    page.value = 1;
    fetchLinks();
}
onMounted(boot);
onUnmounted(() => {
    requestController?.abort();
    clearTimeout(searchTimer);
    clearTimeout(noticeTimer);
});
</script>
<template>
    <div v-if="initial" class="boot-state" role="status">
        Opening your workspace…
    </div>
    <div v-else-if="!user && error" class="boot-state">
        <p role="alert">{{ error }}</p>
        <button class="primary" @click="boot">Try again</button>
    </div>
    <AuthScreen
        v-else-if="!user"
        :registration-enabled="registration"
        @authenticated="signedIn"
    />
    <div v-else class="workspace">
        <a class="skip-link" href="#main">Skip to your links</a>
        <aside class="sidebar">
            <a class="brand" href="/app"
                ><span class="brand-mark">r.</span> relay</a
            >
            <p class="sidebar-label">WORKSPACE</p>
            <nav aria-label="Workspace">
                <button
                    :class="{ selected: view === 'links' }"
                    @click="changeView('links')"
                >
                    <Link2 :size="19" /> All links
                    <span>{{ stats?.total_links ?? 0 }}</span></button
                ><button
                    :class="{ selected: view === 'analytics' }"
                    @click="changeView('analytics')"
                >
                    <BarChart3 :size="19" /> Analytics</button
                ><button
                    :class="{ selected: view === 'archived' }"
                    @click="changeView('archived')"
                >
                    <Archive :size="19" /> Archive
                </button>
            </nav>
            <div class="sidebar-bottom">
                <div class="privacy-note">
                    <ShieldCheck :size="22" />
                    <p>
                        Measure the journey.<br /><strong
                            >Respect the visitor.</strong
                        >
                    </p>
                    <span>Aggregate clicks.<br />No visitor profiles.</span>
                </div>
                <div class="profile">
                    <span class="avatar">{{ initials }}</span>
                    <div>
                        <strong>{{ user.name }}</strong
                        ><span>Personal workspace</span>
                    </div>
                    <button
                        class="icon-button"
                        aria-label="Sign out"
                        :disabled="accountBusy"
                        @click="logout"
                    >
                        <LogOut :size="18" />
                    </button>
                </div>
            </div>
        </aside>
        <main id="main" class="main-content">
            <header class="topbar">
                <span
                    >YOUR WORKSPACE <span class="slash">/</span>
                    <strong>{{
                        view === "archived"
                            ? "Archive"
                            : view === "analytics"
                              ? "Analytics"
                              : "Overview"
                    }}</strong></span
                ><span class="topbar-note"
                    >A little shorter. A lot clearer.</span
                >
            </header>
            <div class="page-body">
                <section class="page-heading">
                    <div>
                        <p class="eyebrow">EVERY LINK HAS A NEXT CHAPTER</p>
                        <h1>
                            {{
                                view === "archived"
                                    ? "Safely set aside."
                                    : view === "analytics"
                                      ? "Follow the journey."
                                      : "Make the connection."
                            }}
                        </h1>
                        <p class="muted">
                            {{
                                view === "archived"
                                    ? "Old links, kept for later. Restore them whenever you need."
                                    : view === "analytics"
                                      ? "A clear view of the places your links are taking people."
                                      : "Your ideas, destinations and next big things. All linked up."
                            }}
                        </p>
                    </div>
                    <button class="primary" @click="openEditor(null)">
                        <Plus :size="18" /> Create link
                    </button>
                </section>
                <p v-if="error" class="error" role="alert">
                    {{ error }}
                    <button class="text-button" @click="refresh">Retry</button>
                </p>
                <section
                    v-if="stats && view !== 'archived'"
                    class="overview"
                    aria-label="Link overview"
                >
                    <div class="metrics-column">
                        <div class="metric">
                            <span>Total clicks <ArrowUpRight :size="16" /></span
                            ><strong>{{
                                stats.total_clicks.toLocaleString()
                            }}</strong
                            ><small>Across all your links</small>
                        </div>
                        <div class="metric-small">
                            <div>
                                <span>Links in your workspace</span
                                ><strong>{{ stats.total_links }}</strong>
                            </div>
                            <div>
                                <span>Active right now</span
                                ><strong
                                    >{{ stats.active_links
                                    }}<i class="green-dot"
                                /></strong>
                            </div>
                        </div>
                    </div>
                    <div class="traffic-panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Little links, real momentum.</h2>
                                <p>
                                    {{ stats.recent_clicks.toLocaleString() }}
                                    clicks in the last 14 days
                                </p>
                            </div>
                            <span class="period-pill">Last 14 days</span>
                        </div>
                        <TrafficChart :days="stats.days" />
                    </div>
                </section>
                <section v-if="view === 'analytics'" class="analytics-note">
                    <ShieldCheck />
                    <div>
                        <h2>
                            The count tells a story. It doesn’t identify a
                            person.
                        </h2>
                        <p>
                            Every successful GET redirect adds one click. Repeat
                            visits and bots can count; these are not unique
                            visitors. HEAD requests are excluded. All dates use
                            UTC.
                        </p>
                        <p>
                            No raw IP addresses, referrers, user agents or
                            visitor cookies are stored by link analytics.
                        </p>
                    </div>
                </section>
                <section
                    v-else
                    class="links-section"
                    aria-labelledby="links-heading"
                >
                    <div class="list-heading">
                        <h2 id="links-heading">
                            {{
                                view === "archived"
                                    ? "Archived links"
                                    : "Your links"
                            }}
                            <span>{{ total }}</span>
                        </h2>
                        <div class="filters">
                            <label class="search"
                                ><Search :size="17" /><input
                                    v-model="search"
                                    aria-label="Search links"
                                    placeholder="Search by title or short link" /></label
                            ><select
                                v-if="view !== 'archived'"
                                v-model="status"
                                aria-label="Filter by status"
                                @change="filter"
                            >
                                <option value="">All statuses</option>
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="expired">Expired</option></select
                            ><button
                                class="icon-button refresh-button"
                                aria-label="Refresh links"
                                :disabled="loading"
                                @click="refresh"
                            >
                                <RefreshCw :size="16" />
                            </button>
                        </div>
                    </div>
                    <div v-if="loading" class="list-loading" role="status">
                        Refreshing your links…
                    </div>
                    <div v-else-if="!links.length" class="empty-state">
                        <Link2 :size="30" />
                        <h3>
                            {{
                                search || status
                                    ? "No links match this view."
                                    : "Your next connection starts here."
                            }}
                        </h3>
                        <p>
                            {{
                                view === "archived"
                                    ? "Archived links will appear here."
                                    : search || status
                                      ? "Try another search or status."
                                      : "Create a short link and give your next idea a home."
                            }}
                        </p>
                        <button
                            v-if="!search && !status"
                            class="secondary"
                            @click="openEditor(null)"
                        >
                            Create your first link
                        </button>
                    </div>
                    <LinkList
                        v-else
                        :links="links"
                        :busy-id="busyId"
                        @edit="openEditor"
                        @copy="copy"
                        @status="changeStatus"
                    />
                    <footer class="list-footer">
                        <span
                            >{{ total }} {{ total === 1 ? "link" : "links" }} ·
                            {{
                                view === "archived"
                                    ? "Ready to restore"
                                    : "Made to be shared"
                            }}</span
                        >
                        <div class="pagination">
                            <button
                                class="icon-button"
                                aria-label="Previous page"
                                :disabled="page <= 1 || loading"
                                @click="changePage(page - 1)"
                            >
                                <ChevronLeft :size="17" /></button
                            ><span>Page {{ page }} of {{ lastPage }}</span
                            ><button
                                class="icon-button"
                                aria-label="Next page"
                                :disabled="page >= lastPage || loading"
                                @click="changePage(page + 1)"
                            >
                                <ChevronRight :size="17" />
                            </button>
                        </div>
                    </footer>
                </section>
                <footer class="page-footer">
                    <span>RELAY / SMALL LINKS, CLEAR DIRECTION</span
                    ><span
                        >Built for what’s next <ArrowUpRight :size="14"
                    /></span>
                </footer>
            </div>
        </main>
        <div v-if="notice" class="toast" role="status">{{ notice }}</div>
        <LinkEditor
            v-if="editing !== undefined"
            :key="editing?.id ?? 'new'"
            :link="editing"
            @close="editing = undefined"
            @saved="saved"
        />
    </div>
</template>
