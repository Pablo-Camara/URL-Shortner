<script setup>
import { reactive, ref } from "vue";
import { ArrowUpRight, Link2 } from "@lucide/vue";
import { api } from "../lib/api";
defineProps({ registrationEnabled: Boolean });
const emit = defineEmits(["authenticated"]);
const register = ref(false),
    busy = ref(false),
    error = ref("");
const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});
async function submit() {
    busy.value = true;
    error.value = "";
    try {
        const session = await api(register.value ? "/register" : "/login", {
            method: "POST",
            body: form,
        });
        emit("authenticated", session.user);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
}
</script>
<template>
    <main class="auth-layout">
        <section class="auth-story">
            <a class="brand" href="/app"
                ><span class="brand-mark">r.</span> relay</a
            >
            <div>
                <p class="eyebrow">LESS URL. MORE POSSIBILITY.</p>
                <h1>Good things<br />deserve a<br /><em>shorter link.</em></h1>
                <p class="story-copy">
                    A considered home for the links you share.<br />Create,
                    refine and see where they go.
                </p>
            </div>
            <div class="story-footer">
                <span><Link2 :size="18" /> Small links. Clear direction.</span
                ><span>01 — ∞</span>
            </div>
        </section>
        <section class="auth-form-wrap">
            <form class="auth-form" @submit.prevent="submit">
                <p class="eyebrow">YOUR LINK WORKSPACE</p>
                <h2>
                    {{
                        register ? "Make room for your ideas." : "Welcome back."
                    }}
                </h2>
                <p class="muted">
                    {{
                        register
                            ? "Create an account to start sharing."
                            : "Sign in to pick up where you left off."
                    }}
                </p>
                <label v-if="register"
                    >Your name<input
                        v-model="form.name"
                        autocomplete="name"
                        required
                        maxlength="80"
                /></label>
                <label
                    >Email address<input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        required
                        maxlength="255"
                        placeholder="you@example.com"
                /></label>
                <label
                    >Password<input
                        v-model="form.password"
                        type="password"
                        :autocomplete="
                            register ? 'new-password' : 'current-password'
                        "
                        required
                        :minlength="register ? 12 : undefined"
                        maxlength="255"
                /></label>
                <label v-if="register"
                    >Confirm password<input
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        minlength="12"
                /></label>
                <p v-if="register" class="field-hint">
                    Use at least 12 characters.
                </p>
                <p v-if="error" class="error" role="alert">{{ error }}</p>
                <button class="primary wide" :disabled="busy">
                    {{
                        busy
                            ? "One moment…"
                            : register
                              ? "Create account"
                              : "Sign in"
                    }}<ArrowUpRight :size="18" />
                </button>
                <p v-if="registrationEnabled" class="auth-switch">
                    {{ register ? "Already have an account?" : "New here?" }}
                    <button
                        type="button"
                        class="text-button"
                        @click="
                            register = !register;
                            error = '';
                        "
                    >
                        {{ register ? "Sign in" : "Create an account" }}
                    </button>
                </p>
                <p class="auth-note">Your links, neatly in one place.</p>
            </form>
        </section>
    </main>
</template>
