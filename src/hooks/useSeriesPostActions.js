import { reorderPosts, removePostFromList } from '../utils/postHelpers';
import { updateSeriesOrder } from '../services/seriesApiExports';

export const useSeriesPostActions = (
  selectedSeriesIds,
  orderedPosts,
  setOrderedPosts
) => {

  const saveOrderToDB = (posts) => {
    if (!selectedSeriesIds) return;
    updateSeriesOrder(selectedSeriesIds, posts);
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