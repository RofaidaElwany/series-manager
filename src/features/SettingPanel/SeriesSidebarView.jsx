import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { PanelBody } from '@wordpress/components';
import { SeriesSelector } from './components/SeriesSelector';
import { SeriesPostsList } from './components/SeriesPostsList';

const SeriesSidebarView = ({
  selectedSeriesIds=[],
  activeSeriesId,
  onSetActiveSeries,
  seriesTerms,
  isResolvingTerms,
  orderedPosts,
  postType,
  onChangeSeries,
  onCreateSeries,
  onReorder,
  onDelete,
  onSave,
  onCancel,
  hasUnsavedChanges,
}) => {
  // Format post type label (convert 'post' to 'Posts', 'page' to 'Pages', etc.)
  const getPostTypeLabel = () => {
    if (!postType) return 'Series';
    const postTypeObj = wp?.data?.select('core').getPostType(postType);
    if (postTypeObj?.labels?.singular_name) {
      return `Series for ${postTypeObj.labels.singular_name}s`;
    }
    return `Series for ${postType}s`;
  };

  // Get the active series name
  const getActiveSeriesName = () => {
    if (!activeSeriesId || !seriesTerms) return '';
    const activeSeries = seriesTerms.find(term => term.id === activeSeriesId);
    return activeSeries ? activeSeries.name : '';
  };
  return (
    <PluginDocumentSettingPanel
      name="sm-series-sidebar"
      //title={series manager}
      title={getPostTypeLabel()}
    >
      <PanelBody>

        <SeriesSelector
          selectedSeriesIds={selectedSeriesIds}
          activeSeriesId={activeSeriesId}
          onSetActiveSeries={onSetActiveSeries}
          seriesTerms={seriesTerms}
          isLoading={isResolvingTerms}
          onChangeSeries={onChangeSeries}
          onCreateSeries={onCreateSeries}
        />

        <SeriesPostsList
          posts={orderedPosts}
          onReorder={onReorder}
          onDelete={onDelete}
          seriesName={getActiveSeriesName()}
          onSave={onSave}
          onCancel={onCancel}
          hasUnsavedChanges={hasUnsavedChanges}
        />

      </PanelBody>
    </PluginDocumentSettingPanel>
  );
};

export { SeriesSidebarView };
