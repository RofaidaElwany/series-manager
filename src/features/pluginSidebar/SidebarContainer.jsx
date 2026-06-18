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
  const [layoutPositions, setLayoutPositions] = useState({});
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
  const getPosition = (term) =>
    layoutPositions[term.id] || term.settings.position || "bottom";

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


  /**
   * Handle layout position changes.
   * Update local state first,
   * then persist changes through the API.
   */
  const onChangeLayoutPosition = async (termId, position) => {
    setError("");

    const nextLayoutPositions = {
      ...layoutPositions,
      [termId]: position,
    };

    /**
     * Update local state.
     */
    setLayoutPositions((current) => ({
      ...current,
      [termId]: position,
    }));

    setSavingTermId(termId);

    try {
      await updateSeriesSettings(termId, { position });
    } catch (err) {
      setError(err.message || "Failed to update series settings");
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
      getPosition={getPosition}
      getVariant={getVariant}
      onChangeLayoutPosition={onChangeLayoutPosition}
      onChangeLayoutVariant={onChangeLayoutVariant}
      getStyleSetting={getStyleSetting}
      onChangeStyleSetting={onChangeStyleSetting}
      onChangeStyleSettings={onChangeStyleSettings}
      onResetStyleSettings={onResetStyleSettings}
    />
  );
}