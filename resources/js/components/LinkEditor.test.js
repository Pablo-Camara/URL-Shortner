import { afterEach, beforeEach, it, expect, vi } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import LinkEditor from "./LinkEditor.vue";
import { api } from "../lib/api";
vi.mock("../lib/api", async (importOriginal) => ({
    ...(await importOriginal()),
    api: vi.fn(),
}));
beforeEach(() => {
    HTMLDialogElement.prototype.showModal = vi.fn();
    vi.clearAllMocks();
});
afterEach(() => (document.body.innerHTML = ""));
it("retains the draft after a failed save and omits alias on edits", async () => {
    api.mockRejectedValue(new Error("Changed in another tab."));
    const wrapper = mount(LinkEditor, {
        attachTo: document.body,
        props: {
            link: {
                id: 3,
                title: "Original",
                url: "https://example.com",
                alias: "original",
                version: 7,
                history: [],
            },
        },
    });
    await wrapper.find('input[maxlength="120"]').setValue("My unsaved change");
    await wrapper.find("form").trigger("submit");
    await flushPromises();
    expect(wrapper.find('input[maxlength="120"]').element.value).toBe(
        "My unsaved change",
    );
    expect(wrapper.find('[role="alert"]').text()).toContain(
        "Changed in another tab.",
    );
    expect(api).toHaveBeenCalledWith(
        "/links/3",
        expect.objectContaining({
            method: "PATCH",
            body: expect.objectContaining({
                title: "My unsaved change",
                version: 7,
            }),
        }),
    );
    expect(api.mock.calls[0][1].body).not.toHaveProperty("alias");
    expect(wrapper.emitted("saved")).toBeUndefined();
    wrapper.unmount();
});
it("creates a link without inventing an optional alias or expiry", async () => {
    api.mockResolvedValue({ data: { id: 8 } });
    const wrapper = mount(LinkEditor, { attachTo: document.body });
    await wrapper.find('input[maxlength="120"]').setValue("New link");
    await wrapper.find('input[type="url"]').setValue("https://example.com");
    await wrapper.find("form").trigger("submit");
    await flushPromises();
    expect(api).toHaveBeenCalledWith("/links", {
        method: "POST",
        body: {
            title: "New link",
            url: "https://example.com",
            alias: null,
            expires_at: null,
        },
    });
    expect(wrapper.emitted("saved")[0]).toEqual([{ id: 8 }]);
    wrapper.unmount();
});
