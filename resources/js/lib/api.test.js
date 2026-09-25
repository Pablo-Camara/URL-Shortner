import { afterEach, describe, it, expect, vi } from "vitest";
import { api, setCsrfToken, compactUrl, localDateTime } from "./api";
afterEach(() => vi.unstubAllGlobals());
describe("HTTP boundary", () => {
    it("sends session credentials and CSRF with structured mutation data", async () => {
        setCsrfToken("csrf-test");
        const fetch = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ data: { id: 1 } }),
        });
        vi.stubGlobal("fetch", fetch);
        await api("/links", { method: "POST", body: { title: "A" } });
        expect(fetch).toHaveBeenCalledWith(
            "/api/links",
            expect.objectContaining({
                credentials: "same-origin",
                body: '{"title":"A"}',
                headers: expect.objectContaining({
                    "X-CSRF-TOKEN": "csrf-test",
                }),
            }),
        );
    });
    it("surfaces validation and conflict errors", async () => {
        vi.stubGlobal(
            "fetch",
            vi.fn().mockResolvedValue({
                ok: false,
                status: 409,
                json: async () => ({ message: "Changed in another tab." }),
            }),
        );
        await expect(api("/links/1")).rejects.toMatchObject({
            status: 409,
            message: "Changed in another tab.",
        });
    });
    it("does not expose server exception details", async () => {
        vi.stubGlobal(
            "fetch",
            vi.fn().mockResolvedValue({
                ok: false,
                status: 500,
                json: async () => ({ message: "SQL with secrets" }),
            }),
        );
        await expect(api("/links")).rejects.toThrow("Something went wrong.");
    });
    it("keeps aborts separate from connection failures", async () => {
        vi.stubGlobal(
            "fetch",
            vi
                .fn()
                .mockRejectedValue(new DOMException("Aborted", "AbortError")),
        );
        await expect(api("/links")).rejects.toMatchObject({
            name: "AbortError",
        });
    });
    it("formats addresses without changing their path", () => {
        expect(compactUrl("https://example.com/https://path")).toBe(
            "example.com/https://path",
        );
        expect(localDateTime(null)).toBe("");
    });
});
