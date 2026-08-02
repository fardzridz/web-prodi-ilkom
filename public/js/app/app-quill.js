(function initQuillEditors() {
    if (typeof window.Quill === "undefined") {
        return;
    }

    const FONT_WHITELIST = ["Fakt", "Grold", "GroldSlim", "GroldRounded", "Gotcha", "default"];

    const FontClass = window.Quill.import("formats/font");
    if (FontClass && Array.isArray(FontClass.whitelist)) {
        FontClass.whitelist = FONT_WHITELIST;
        window.Quill.register(FontClass, true);
    }

    const FORMATS = [
        "background", "bold", "color", "font", "code",
        "italic", "link", "size", "strike", "script", "underline",
        "blockquote", "header", "indent", "list",
        "align", "direction", "code-block",
        "formula", "image", "video"
    ];

    const TOOLBAR = [
        [{ font: FONT_WHITELIST }, { size: ["small", false, "large", "huge"] }],
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["bold", "italic", "underline", "strike"],
        [{ color: [] }, { background: [] }],
        [{ script: "sub" }, { script: "super" }],
        [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ align: [] }],
        ["blockquote", "code-block"],
        ["link", "image", "video"],
        ["clean"]
    ];

    const editors = document.querySelectorAll(".quill-editor");
    editors.forEach((element) => {
        const hidden = document.getElementById(element.id + "-hidden");
        const initialValue = hidden ? hidden.value : "";

        let initialHtml = "";
        if (initialValue) {
            const trimmed = initialValue.trim();
            if (trimmed.startsWith("{")) {
                const probeHost = document.createElement("div");
                probeHost.style.cssText = "position:absolute;left:-99999px;top:0;width:1px;height:1px;overflow:hidden;visibility:hidden;pointer-events:none;";
                document.body.appendChild(probeHost);
                const probeEditor = document.createElement("div");
                probeHost.appendChild(probeEditor);
                try {
                    const probeQuill = new window.Quill(probeEditor, {
                        readOnly: true,
                        modules: { toolbar: false }
                    });
                    probeQuill.setContents(JSON.parse(initialValue));
                    initialHtml = probeQuill.getSemanticHTML().replace(/&nbsp;/g, " ");
                } catch (error) {
                    console.error("Quill Delta conversion failed:", error.message);
                }
                probeHost.remove();
            } else {
                initialHtml = initialValue;
            }
        }
        if (hidden) hidden.value = initialHtml;

        const quill = new window.Quill(element, {
            theme: "snow",
            placeholder: "Tuliskan konten di sini...",
            formats: FORMATS,
            modules: { toolbar: TOOLBAR }
        });

        if (initialHtml) {
            try {
                quill.clipboard.dangerouslyPasteHTML(initialHtml);
            } catch (error) {
                console.error("Quill HTML paste failed:", error.message);
            }
        }

        const syncHidden = () => {
            if (hidden) {
                hidden.value = quill.getSemanticHTML().replace(/&nbsp;/g, " ");
            }
        };

        quill.on("text-change", syncHidden);
    });
})();
