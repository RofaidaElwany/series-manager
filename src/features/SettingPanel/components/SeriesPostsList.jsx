import {
  DndContext,
  closestCenter,
  PointerSensor,
  useSensor,
  useSensors,
  DragOverlay
} from '@dnd-kit/core';
import {
  SortableContext,
  verticalListSortingStrategy
} from '@dnd-kit/sortable';
import { useState } from '@wordpress/element';
import { SortableItem } from './SortableItem';
import { DragOverlayItem } from './DragOverlayItem';
import '../../../index.css';

const SeriesPostsList = ({
  posts,
  onReorder,
  onDelete,
  seriesName,
  onSave,
  onCancel,
}) => {
  const [activePost, setActivePost] = useState(null);
  const sensors = useSensors(useSensor(PointerSensor));

  const handleDragStart = (event) => {
    const post = posts.find(p => p.id === event.active.id);
    setActivePost(post);
  };

  const handleDragEnd = (event) => {
    const { active, over } = event;
    setActivePost(null);

    if (!over) return;

    onReorder(active.id, over.id);
  };

  const handleDragCancel = () => {
    setActivePost(null);
  };

  if (!posts || posts.length === 0) {
    return null;
  }

  return (
    <div className="series-posts-list">
      {seriesName && (
        <h4 className="text-lg font-semibold mb-2">{seriesName}</h4>
      )}
      
      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
        onDragCancel={handleDragCancel}
      >
        <SortableContext
          items={posts.map(p => p.id)}
          strategy={verticalListSortingStrategy}
        >
          <ul className="flex flex-col list-none p-0 mt-4 gap-2">
            {posts.map(post => (
              <SortableItem
                key={post.id}
                id={post.id}
                post={post}
                onDelete={() => onDelete(post)}
              />
            ))}
          </ul>
        </SortableContext>
        
        <DragOverlay adjustScale={false}>
          {activePost && (
            <DragOverlayItem
              post={activePost}
              className="font-mono text-sm px-3 py-2 rounded-md border bg-blue-200 border-blue-300 shadow-lg cursor-grabbing"
            />
          )}
        </DragOverlay>
      </DndContext>

      {/* Buttons now render by default whenever the component renders posts */}
      <div className="flex gap-2 mt-4">
        <button
          onClick={onCancel}
          className="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors"
        >
          Cancel
        </button>
        <button
          onClick={onSave}
          className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
        >
          Save
        </button>
      </div>
    </div>
  );
};

export { SeriesPostsList };