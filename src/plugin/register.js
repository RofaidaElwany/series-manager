import domReady from "@wordpress/dom-ready";
import { registerPlugin } from "@wordpress/plugins";
import { SeriesSidebarContainer } from "../components/seriesSidebar/SeriesSidebarContainer";
import { SidebarContainer } from "./sidebar/SidebarContainer";

const removeDefaultSeriesPanel = () => {
  const editorDispatch = wp?.data?.dispatch("core/editor");

  if (editorDispatch?.removeEditorPanel) {
    editorDispatch.removeEditorPanel("taxonomy-panel-series");
  }
};

export const registerSeriesPlugin = () => {
  /* Remove the default series taxonomy panel from the post editor sidebar * to replace it with our custom SeriesSidebarContainer which provides enhanced functionality. */
  domReady(removeDefaultSeriesPanel);

  if (!window.smSeriesSidebarRegistered) {
    registerPlugin("sm-series-sidebar", {
      render: SeriesSidebarContainer,
    });

    window.smSeriesSidebarRegistered = true;
  }

  if (!window.pluginSidebarRegistered) {
    registerPlugin("sm-sidebar", {
      render: SidebarContainer,
    });
    window.pluginSidebarRegistered = true;
  }
};
