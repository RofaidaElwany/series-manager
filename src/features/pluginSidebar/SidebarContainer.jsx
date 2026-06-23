import { SidebarView } from "./SidebarView";
import { createBlock } from "@wordpress/blocks";
import { useDispatch, useSelect } from "@wordpress/data";
import {
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "@wordpress/element";
import { useSeriesTerms } from "../../hooks/useSeriesTerms";
import { updateSeriesSettings } from "../../services/seriesApiExports";
import { normalizeStyleUpdates } from "./components/utils/styleSettings";

const SPACING_PREVIEW_EVENT = "sm-series-spacing-preview";

const resolveSpacingCssValue = (value) => {
  if (value === undefined || value === null || value === "") {
    return "0px";
  }

  const stringValue = String(value).trim();

  if (stringValue === "0") {
    return "0px";
  }

  const presetMatch = stringValue.match(/^var:preset\|spacing\|([a-z0-9-]+)$/i);

  if (presetMatch) {
    return `var(--wp--preset--spacing--${presetMatch[1]})`;
  }

  if (/^\d+(\.\d+)?(px|rem|em|%|vh|vw)$/i.test(stringValue)) {
    return stringValue;
  }

  if (/^\d+(\.\d+)?$/.test(stringValue)) {
    return `${stringValue}px`;
  }

  return stringValue.startsWith("var(--") ? stringValue : "0px";
};

const getSpacingSideValue = (spacing, side) => {
  if (!spacing || typeof spacing !== "object") {
    return "0px";
  }

  if (spacing[side] !== undefined && spacing[side] !== null && spacing[side] !== "") {
    return resolveSpacingCssValue(spacing[side]);
  }

  if ((side === "left" || side === "right") && spacing.horizontal) {
    return resolveSpacingCssValue(spacing.horizontal);
  }

  if ((side === "top" || side === "bottom") && spacing.vertical) {
    return resolveSpacingCssValue(spacing.vertical);
  }

  return "0px";
};

const getEditorDocuments = () => {
  const docs = [document];

  document.querySelectorAll("iframe").forEach((iframe) => {
    try {
      if (iframe.contentDocument) {
        docs.push(iframe.contentDocument);
      }
    } catch (error) {
      // Ignore cross-origin frames.
    }
  });

  return docs;
};

const clearSpacingPreview = (element) => {
  element.classList.remove(
    "sm-series-spacing-preview",
    "sm-series-spacing-preview--padding",
    "sm-series-spacing-preview--margin",
  );
};

const applySpacingPreview = ({ termId, style, changedKeys }) => {
  if (!termId || !Array.isArray(changedKeys) || changedKeys.length === 0) {
    return;
  }

  const selector = `[data-sm-series-term-id="${termId}"]`;
  const target = getEditorDocuments()
    .flatMap((doc) => Array.from(doc.querySelectorAll(selector)))
    .find((element) => element.closest(".sm-series-editor-root"));

  if (!target) {
    return;
  }

  clearSpacingPreview(target);

  ["padding", "margin"].forEach((type) => {
    ["top", "right", "bottom", "left"].forEach((side) => {
      target.style.setProperty(
        `--sm-preview-${type}-${side}`,
        getSpacingSideValue(style?.[type], side),
      );
    });
  });

  target.classList.add("sm-series-spacing-preview");

  if (changedKeys.includes("padding")) {
    target.classList.add("sm-series-spacing-preview--padding");
  }

  if (changedKeys.includes("margin")) {
    target.classList.add("sm-series-spacing-preview--margin");
  }

  window.clearTimeout(target.smSeriesSpacingPreviewTimer);
  target.smSeriesSpacingPreviewTimer = window.setTimeout(() => {
    clearSpacingPreview(target);
  }, 1800);
};

export function SidebarContainer() {
  /**
   * Get editor and block editor data.
   */
  const { postType, currentSeries, blocks } = useSelect((select) => {
    const editor = select("core/editor");
    const blockEditor = select("core/block-editor");

    return {
      postType: editor.getCurrentPostType(),
      currentSeries: editor.getEditedPostAttribute("series") || [],
      blocks: blockEditor.getBlocks(),
    };
  }, []);


  /**
   * Local component state.
   */
  const [layoutVariants, setLayoutVariants] = useState({});
  const [layoutStyles, setLayoutStyles] = useState({});
  const layoutStylesRef = useRef({});
  const [savingTermId, setSavingTermId] = useState(null);
  const [error, setError] = useState("");

  /**
   * Convert series values into numeric IDs.
   */
  const normalizeSeriesId = (value) => {
    if (value == null) {
      return null;
    }

    if (typeof value === "object") {
      const candidate = value.id ?? value.term_id ?? value;
      const num = Number(candidate);

      return Number.isNaN(num) ? null : num;
    }

    const num = Number(value);

    return Number.isNaN(num) ? null : num;
  };

  /**
   * Normalize selected series IDs.
   */
  const selectedSeriesIds = currentSeries
    .map(normalizeSeriesId)
    .filter(Boolean);

  /**
   * Load all available series terms.
   */
  const { seriesTerms, isResolvingTerms } = useSeriesTerms(postType);

  /**
   * Create a stable key for memo dependencies.
   */
  const selectedSeriesKey = selectedSeriesIds.join(",");

  /**
   * Get the selected series term objects.
   */
  const selectedSeries = useMemo(
    () =>
      seriesTerms.filter((term) =>
        selectedSeriesIds.includes(Number(term.id)),
      ),
    [seriesTerms, selectedSeriesKey],
  );

  /**
   * Get the current layout position for a term.
   * Priority:
   * 1. Local state
   * 2. Saved settings
   * 3. Default value
   */

  const getVariant = (term) =>
    layoutVariants[term.id] || term.settings.layout || "link-list";

  const getStyleSetting = (term, key) => {
    if (layoutStyles[term.id] && layoutStyles[term.id][key] !== undefined) {
      return layoutStyles[term.id][key];
    }

    if (term.settings?.style?.[key] !== undefined) {
      return term.settings.style[key];
    }

    if (key === "padding" || key === "margin" || key === "border") {
      return undefined;
    }

    return "";
  };

  const onChangeStyleSettings = async (termId, updates) => {
    setError("");

    const cleanedUpdates = normalizeStyleUpdates(updates);
    const currentTerm = selectedSeries.find((t) => t.id === termId);
    const existingStyle = currentTerm?.settings?.style || {};

    const updatedStyle = {
      ...existingStyle,
      ...(layoutStylesRef.current[termId] || {}),
      ...cleanedUpdates,
    };

    Object.entries(cleanedUpdates).forEach(([key, value]) => {
      if (value === undefined) {
        delete updatedStyle[key];
      }
    });

    layoutStylesRef.current[termId] = updatedStyle;

    const spacingKeys = ["padding", "margin"].filter((key) =>
      Object.prototype.hasOwnProperty.call(updates, key),
    );

    if (spacingKeys.length > 0) {
      window.dispatchEvent(
        new CustomEvent(SPACING_PREVIEW_EVENT, {
          detail: {
            termId,
            style: updatedStyle,
            changedKeys: spacingKeys,
          },
        }),
      );
    }

    setLayoutStyles((current) => ({
      ...current,
      [termId]: { ...updatedStyle },
    }));

    setSavingTermId(termId);

    try {
      await updateSeriesSettings(termId, { style: updatedStyle });
      refreshSeriesPreview();
    } catch (err) {
      setError(err.message || "Failed to update style settings");
    } finally {
      setSavingTermId(null);
    }
  };


  
  const refreshSeriesPreview = () => {
    if (typeof window !== "undefined" && window.dispatchEvent) {
      window.dispatchEvent(
        new CustomEvent("sm-series-preview-refresh", {
          detail: { termId: null },
        }),
      );
    }
  };

  useEffect(() => {
    const handleSpacingPreview = (event) => {
      applySpacingPreview(event.detail || {});
    };

    window.addEventListener(SPACING_PREVIEW_EVENT, handleSpacingPreview);

    return () => {
      window.removeEventListener(SPACING_PREVIEW_EVENT, handleSpacingPreview);
    };
  }, []);

  const onChangeLayoutVariant = async (termId, layout) => {
    setError("");

    const nextLayoutVariants = {
      ...layoutVariants,
      [termId]: layout,
    };

    setLayoutVariants((current) => ({
      ...current,
      [termId]: layout,
    }));

    setSavingTermId(termId);

    try {
      await updateSeriesSettings(termId, { layout });
      refreshSeriesPreview();
    } catch (err) {
      setError(err.message || "Failed to update series settings");
    } finally {
      setSavingTermId(null);
    }
  };

  const onChangeStyleSetting = async (termId, key, value) => {
    await onChangeStyleSettings(termId, { [key]: value });
  };

  const onResetStyleSettings = async (termId) => {
    setError("");

    layoutStylesRef.current[termId] = {
      titleColor: "",
      headerBackgroundColor: "",
      buttonColor: "",
    };

    setLayoutStyles((current) => ({
      ...current,
      [termId]: {
        titleColor: "",
        headerBackgroundColor: "",
        buttonColor: "",
        padding: undefined,
        margin: undefined,
        border: undefined,
      },
    }));

    setSavingTermId(termId);

    try {
      await updateSeriesSettings(termId, { style: {} });
      refreshSeriesPreview();
    } catch (err) {
      setError(err.message || "Failed to reset style settings");
    } finally {
      setSavingTermId(null);
    }
  };

  /**
   * Render the sidebar view.
   */
  return (
    <SidebarView
      selectedSeries={selectedSeries}
      isLoading={isResolvingTerms}
      savingTermId={savingTermId}
      error={error}
      getVariant={getVariant}
      onChangeLayoutVariant={onChangeLayoutVariant}
      getStyleSetting={getStyleSetting}
      onChangeStyleSetting={onChangeStyleSetting}
      onChangeStyleSettings={onChangeStyleSettings}
      onResetStyleSettings={onResetStyleSettings}
    />
  );
}
