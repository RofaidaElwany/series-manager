import { reorderPosts, removePostFromList } from '../utils/postHelpers';
import { updateSeriesOrder } from '../services/seriesApiExports';

export const useSeriesPostActions = (
  activeSeriesId,
  orderedPosts,
  setOrderedPosts
) => {
  const refreshSeriesPreview = () => {
    if (typeof window !== 'undefined' && window.dispatchEvent) {
      window.dispatchEvent(
        new CustomEvent('sm-series-preview-refresh', {
          detail: { termId: activeSeriesId },
        })
      );
    }
  };

  const saveOrderToDB = (posts) => {
    if (!activeSeriesId) {
      return Promise.resolve();
    }

    return Promise.resolve(updateSeriesOrder(activeSeriesId, posts)).then((response) => {
      refreshSeriesPreview();
      return response;
    });
  };

  const handleReorder = (activeId, overId) => {
    const newPosts = reorderPosts(
      orderedPosts,
      activeId,
      overId
    );

    setOrderedPosts(newPosts);
    // Removed automatic save - will save only on Save button click
  };

  const handleDelete = (postToDelete) => {
    const updatedPosts = removePostFromList(
      orderedPosts,
      postToDelete.id
    );

    setOrderedPosts(updatedPosts);
    // Removed automatic save - will save only on Save button click
  };

  return {
    handleReorder,
    handleDelete,
    saveOrderToDB,
  };
};
