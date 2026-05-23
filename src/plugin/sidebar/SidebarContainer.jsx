import { SidebarView } from "./SidebarView";
import { createBlock } from "@wordpress/blocks";
import { useDispatch, useSelect } from "@wordpress/data";
import { useCallback, useEffect, useMemo, useRef, useState } from "@wordpress/element";

import { useSeriesTerms } from "../../hooks/useSeriesTerms";
import { updateSeriesLayoutSettings } from "../../services/seriesApiExports";

export function SidebarContainer() {
  const { postType, currentSeries, blocks } = useSelect((select) => {
    const editor = select("core/editor");
    const blockEditor = select("core/block-editor");

    return {
      postType: editor.getCurrentPostType(),
      currentSeries: editor.getEditedPostAttribute("series") || [],
      blocks: blockEditor.getBlocks(),
    };
  }, []);
  const { insertBlocks, moveBlocksToPosition, removeBlocks } =
    useDispatch("core/block-editor");

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

  const selectedSeriesIds = currentSeries.map(normalizeSeriesId).filter(Boolean);
  const { seriesTerms, isResolvingTerms } = useSeriesTerms(postType);
  const [layoutPositions, setLayoutPositions] = useState({});
  const [savingTermId, setSavingTermId] = useState(null);
  const [error, setError] = useState("");
  const lastAppliedPlacementRef = useRef("");

  const selectedSeriesKey = selectedSeriesIds.join(",");
  const selectedSeries = useMemo(
    () =>
      seriesTerms.filter((term) =>
        selectedSeriesIds.includes(Number(term.id)),
    ),
    [seriesTerms, selectedSeriesKey],
  );

  const getPosition = (term) =>
    layoutPositions[term.id] || term.layoutPosition || "bottom";

  const resolvePosition = useCallback(
    (positions = layoutPositions) =>
      selectedSeries.some(
        (term) => (positions[term.id] || term.layoutPosition) === "top",
      )
        ? "top"
        : "bottom",
    [layoutPositions, selectedSeries],
  );

  const placeSeriesBlock = useCallback(
    (position) => {
      const seriesBlocks = blocks.filter(
        (block) => block.name === "series-manager/series-list",
      );
      const seriesBlockIndex = blocks.findIndex(
        (block) => block.clientId === seriesBlocks[0]?.clientId,
      );
      const targetIndex = position === "top" ? 0 : blocks.length;

      if (seriesBlockIndex === -1) {
        insertBlocks(createBlock("series-manager/series-list"), targetIndex);
        return;
      }

      if (seriesBlocks.length > 1) {
        removeBlocks(seriesBlocks.slice(1).map((block) => block.clientId));
        return;
      }

      const isAtTarget =
        position === "top"
          ? seriesBlockIndex === 0
          : seriesBlockIndex === blocks.length - 1;

      if (isAtTarget) {
        return;
      }

      moveBlocksToPosition(
        [blocks[seriesBlockIndex].clientId],
        "",
        "",
        targetIndex,
      );
    },
    [blocks, insertBlocks, moveBlocksToPosition, removeBlocks],
  );

  useEffect(() => {
    if (isResolvingTerms || selectedSeries.length === 0) {
      lastAppliedPlacementRef.current = "";
      return;
    }

    const position = resolvePosition();
    const seriesBlockIndex = blocks.findIndex(
      (block) => block.name === "series-manager/series-list",
    );
    const placementKey = `${position}:${seriesBlockIndex}:${blocks.length}`;

    if (lastAppliedPlacementRef.current === placementKey) {
      return;
    }

    lastAppliedPlacementRef.current = placementKey;
    placeSeriesBlock(position);
  }, [
    blocks,
    isResolvingTerms,
    placeSeriesBlock,
    resolvePosition,
    selectedSeries.length,
  ]);

  const onChangeLayoutPosition = async (termId, position) => {
    setError("");
    const nextLayoutPositions = {
      ...layoutPositions,
      [termId]: position,
    };

    setLayoutPositions((current) => ({
      ...current,
      [termId]: position,
    }));
    placeSeriesBlock(resolvePosition(nextLayoutPositions));
    setSavingTermId(termId);

    try {
      await updateSeriesLayoutSettings(termId, position);
    } catch (err) {
      setError(err.message || "Failed to update series layout settings");
    } finally {
      setSavingTermId(null);
    }
  };

  return (
    <SidebarView
      selectedSeries={selectedSeries}
      isLoading={isResolvingTerms}
      savingTermId={savingTermId}
      error={error}
      getPosition={getPosition}
      onChangeLayoutPosition={onChangeLayoutPosition}
    />
  );
}
