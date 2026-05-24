import { createBlock } from "@wordpress/blocks";
import { useState, useEffect, useRef } from "@wordpress/element";
import { useSelect, useDispatch } from "@wordpress/data";
import { SeriesSidebarView } from "./SeriesSidebarView";
import { useSeriesPosts } from "../../../hooks/useSeriesPosts";
import { useSeriesTerms } from "../../../hooks/useSeriesTerms";
import { usePostSavingSync } from "../../../hooks/usePostSavingSync";
import { useSeriesPostActions } from "../../../hooks/useSeriesPostActions";
import { createSeriesTerm } from "../../../services/seriesApiExports";

const SeriesSidebarContainer = () => {
  /* ========================= Editor Data ========================= */
  // Get necessary data from the editor store
  const { postId, postTitle, postType, currentSeries, blocks } = useSelect(
    (select) => {
      const editor = select("core/editor");
      const blockEditor = select("core/block-editor");

      return {
        postId: editor.getCurrentPostId(),
        postTitle: editor.getEditedPostAttribute("title"),
        postType: editor.getCurrentPostType(),
        currentSeries: editor.getEditedPostAttribute("series") || [],
        blocks: blockEditor.getBlocks(),
      };
    },
  );

  const normalizeSeriesId = (value) => {
    if (value == null) return null;
    if (typeof value === "object") {
      const candidate = value.id ?? value.term_id ?? value;
      const num = Number(candidate);
      return Number.isNaN(num) ? null : num;
    }
    const num = Number(value);
    return Number.isNaN(num) ? null : num;
  };

  // Active series ID for UI state (e.g., highlighting selected series)
  const [activeSeriesId, setActiveSeriesId] = useState(null);
  // Normalize and filter out invalid series IDs

  const selectedSeriesIds = currentSeries
    .map(normalizeSeriesId)
    .filter(Boolean);

  // Default to the most recently selected series if none is active
  useEffect(() => {
    if (!activeSeriesId && selectedSeriesIds.length > 0) {
      setActiveSeriesId(selectedSeriesIds[selectedSeriesIds.length - 1]);
    }
  }, [selectedSeriesIds]);

  const { editPost } = useDispatch("core/editor");
  const { insertBlocks, selectBlock } = useDispatch("core/block-editor");

  /* ========================= Terms ========================= */
  const { seriesTerms, isResolvingTerms } = useSeriesTerms(postType);

  /* =========================    Posts  ========================= */
  const { orderedPosts, setOrderedPosts } = useSeriesPosts(
    activeSeriesId || null,
    postId,
    postTitle,
  );

  // Track original posts for cancel functionality
  const [originalPosts, setOriginalPosts] = useState([]);
  const originalSeriesIdRef = useRef(null);

  // Update original posts when series changes or posts are first loaded
  useEffect(() => {
    if (activeSeriesId !== originalSeriesIdRef.current) {
      originalSeriesIdRef.current = activeSeriesId;
      setOriginalPosts([]);
      return;
    }

    if (
      activeSeriesId &&
      originalPosts.length === 0 &&
      orderedPosts.length > 0
    ) {
      setOriginalPosts([...orderedPosts]);
    }

    if (!activeSeriesId) {
      originalSeriesIdRef.current = null;
      setOriginalPosts([]);
    }
  }, [activeSeriesId, orderedPosts, originalPosts.length]);

  // Check if there are unsaved changes
  const hasUnsavedChanges =
    originalPosts.length > 0 &&
    (originalPosts.length !== orderedPosts.length ||
      originalPosts.some((post, idx) => post.id !== orderedPosts[idx]?.id));

  /* =========================    Post Actions (Reorder, Delete)  ========================= */
  const { handleReorder, handleDelete, saveOrderToDB } = useSeriesPostActions(
    activeSeriesId,
    orderedPosts,
    setOrderedPosts,
  );

  /* =========================    Sync with Post Saving  ========================= */
  usePostSavingSync(activeSeriesId, orderedPosts, saveOrderToDB);

  /* =========================    Ensure Series Block Exists on Mount  ========================= */
  useEffect(() => {
    if (!selectedSeriesIds.length) {
      return;
    }

    const hasSeriesBlock = blocks.some(
      (block) => block.name === "series-manager/series-list",
    );

    if (!hasSeriesBlock) {
      const block = createBlock("series-manager/series-list");

      insertBlocks(block);
    }
  }, [selectedSeriesIds, blocks]);
  /* =========================    Save and Cancel Handlers  ========================= */
  const handleSave = async () => {
    await saveOrderToDB(orderedPosts);
    setOriginalPosts([...orderedPosts]);
  };

  const handleCancel = () => {
    setOrderedPosts([...originalPosts]);
  };

  /* =========================    Handler(Change series)  ========================= */
  // Ensure the series block exists in the editor when changing series
  const ensureSeriesBlockExists = () => {
    const hasSeriesBlock = blocks.some(
      (block) => block.name === "series-manager/series-list",
    );

    if (!hasSeriesBlock) {
      const block = createBlock("series-manager/series-list");
      insertBlocks(block);
      selectBlock(block.clientId);
    }
  };

  const onChangeSeries = (seriesId) => {
    const id = Number(seriesId);

    let updated;
    if (selectedSeriesIds.includes(id)) {
      //remove from series
      updated = selectedSeriesIds.filter((s) => s !== id);

      // If the removed series was active, clear active selection
      if (activeSeriesId === id) {
        setActiveSeriesId(updated[0] || null);
      }
    } else {
      //add to series
      updated = [...selectedSeriesIds, id];
      setActiveSeriesId(id);
      ensureSeriesBlockExists();

      //if no active series, set the newly added one as active
      if (!activeSeriesId) {
        setActiveSeriesId(id);
      }
    }

    editPost({
      series: updated,
    });
  };

  /* =========================    Handler(Create series)  ========================= */
  const handleCreateSeries = async (name) => {
    try {
      const newTerm = await createSeriesTerm(name);

      if (newTerm?.id) {
        onChangeSeries(newTerm.id);
      }
    } catch (err) {
      console.error("Error creating series:", err);
    }
  };

  return (
    <SeriesSidebarView
      activeSeriesId={activeSeriesId}
      onSetActiveSeries={setActiveSeriesId}
      selectedSeriesIds={selectedSeriesIds}
      seriesTerms={seriesTerms}
      isResolvingTerms={isResolvingTerms}
      orderedPosts={orderedPosts}
      postType={postType}
      onChangeSeries={onChangeSeries}
      onCreateSeries={handleCreateSeries}
      onReorder={handleReorder}
      onDelete={handleDelete}
      onSave={handleSave}
      onCancel={handleCancel}
      hasUnsavedChanges={hasUnsavedChanges}
    />
  );
};

export { SeriesSidebarContainer };
