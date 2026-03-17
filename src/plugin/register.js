import { registerPlugin } from '@wordpress/plugins';
import { addFilter } from '@wordpress/hooks';
import { SeriesSidebarContainer } from '../components/seriesSidebar/SeriesSidebarContainer';

export const registerSeriesPlugin = () => {
  addFilter(
    'editor.PostTaxonomyType',
    'sm-series/hide-taxonomy-panel',
    (OriginalComponent) => (props) => {
      console.log('PostTaxonomyType filter called with props:', props); // Add this
      if (props.slug === 'series') {
        console.log('Hiding series taxonomy panel'); // Add this
        return null;
      }
      return <OriginalComponent {...props} />;
    }
  );

  if (!window.smSeriesSidebarRegistered) {
    registerPlugin('sm-series-sidebar', {
      render: SeriesSidebarContainer,
    });

    window.smSeriesSidebarRegistered = true;
  }
};


