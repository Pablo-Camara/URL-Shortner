let csrfToken = "";
export function setCsrfToken(value) {
    csrfToken = value;
}
export async function api(path, options = {}) {
    let response;
    try {
        response = await fetch(`/api${path}`, {
            credentials: "same-origin",
            ...options,
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                ...options.headers,
            },
            body:
                options.body === undefined
                    ? undefined
                    : JSON.stringify(options.body),
        });
    } catch (error) {
        if (error.name === "AbortError") throw error;
        throw new Error(
            "Connection lost. Your changes are still here; try again.",
        );
    }
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        const messages = Object.values(data.errors ?? {}).flat();
        const message =
            response.status === 419
                ? "Your session expired. Reload the page and sign in again."
                : response.status === 401
                  ? "Please sign in again to continue."
                  : response.status >= 500
                    ? "Something went wrong. Please try again shortly."
                    : (messages[0] ??
                      data.message ??
                      "The request could not be completed.");
        throw Object.assign(new Error(message), {
            status: response.status,
            fields: data.errors ?? {},
        });
    }
    if (data.csrf_token) setCsrfToken(data.csrf_token);
    return data;
}
export function compactUrl(value) {
    return String(value ?? "").replace(/^https?:\/\//, "");
}
export function displayDate(value) {
    return new Intl.DateTimeFormat("en", {
        month: "short",
        day: "numeric",
        year: "numeric",
    }).format(new Date(value));
}
export function localDateTime(value) {
    if (!value) return "";
    const date = new Date(value);
    return new Date(date.getTime() - date.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
}
