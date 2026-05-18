import { registerPlugin } from "@wordpress/plugins";
import { addFilter } from "@wordpress/hooks";
import { SeriesSidebarContainer } from "../components/seriesSidebar/SeriesSidebarContainer";
import { useEffect } from "@wordpress/element";

export const registerSeriesPlugin = () => {
  /* Remove the default series taxonomy panel from the post editor sidebar * to replace it with our custom SeriesSidebarContainer which provides enhanced functionality. */
  wp.data.dispatch("core/edit-post").removeEditorPanel("taxonomy-panel-series");

  if (!window.smSeriesSidebarRegistered) {
    registerPlugin("sm-series-sidebar", {
      render: SeriesSidebarContainer,
    });

    window.smSeriesSidebarRegistered = true;
  }
};
