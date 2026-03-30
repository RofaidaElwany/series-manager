import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { PanelBody } from '@wordpress/components';

import { SeriesSelector } from '../SeriesSelector';
import { SeriesPostsList } from '../SeriesPostsList';

const SeriesSidebarView = ({
  selectedSeriesId,
  seriesTerms,
  isResolvingTerms,
  orderedPosts,
  postType,
  onChangeSeries,
  onCreateSeries,
  onReorder,
  onDelete,
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
  return (
    <PluginDocumentSettingPanel
      name="sm-series-sidebar"
      //title={series manager}
      title={getPostTypeLabel()}
    >
      <PanelBody>

        <SeriesSelector
          selectedSeriesId={selectedSeriesId}
          seriesTerms={seriesTerms}
          isLoading={isResolvingTerms}
          onChangeSeries={onChangeSeries}
          onCreateSeries={onCreateSeries}
        />

        <SeriesPostsList
          posts={orderedPosts}
          onReorder={onReorder}
          onDelete={onDelete}
        />

      </PanelBody>
    </PluginDocumentSettingPanel>
  );
};

export { SeriesSidebarView };