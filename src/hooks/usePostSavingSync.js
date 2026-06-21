// usePostSavingSync.js
import { useEffect, useRef } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

export const usePostSavingSync = (
  activeSeriesId,
  orderedPosts,
  saveOrderToDB
) => {
  // Keep refs so the effect always sees fresh values
  // without needing them in the dependency array
  const orderedPostsRef = useRef(orderedPosts);
  const saveOrderToDBRef = useRef(saveOrderToDB);
  const activeSeriesIdRef = useRef(activeSeriesId);

  useEffect(() => { orderedPostsRef.current = orderedPosts; }, [orderedPosts]);
  useEffect(() => { saveOrderToDBRef.current = saveOrderToDB; }, [saveOrderToDB]);
  useEffect(() => { activeSeriesIdRef.current = activeSeriesId; }, [activeSeriesId]);

  const { isSavingPost, isAutosavingPost } = useSelect(
    (select) => ({
      isSavingPost: select('core/editor').isSavingPost(),
      isAutosavingPost: select('core/editor').isAutosavingPost(),
    }),
    []
  );

  useEffect(() => {
    if (
      isSavingPost &&
      !isAutosavingPost &&
      activeSeriesIdRef.current &&
      orderedPostsRef.current.length > 0
    ) {
      saveOrderToDBRef.current(orderedPostsRef.current);
    }
  }, [isSavingPost, isAutosavingPost]); // stable — refs handle fresh values
};