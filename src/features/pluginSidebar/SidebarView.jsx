import { PluginSidebar } from "@wordpress/editor";
import { Notice, PanelBody, Spinner, TabPanel } from "@wordpress/components";
import { __} from "@wordpress/i18n";
import { PositionTab } from "./components/tabs/PositionTab";
import { LayoutTab } from "./components/tabs/LayoutTab";
import { StyleTab } from "./components/tabs/StyleTab";
import "../../index.css";
export function SidebarView({
  selectedSeries,
  isLoading,
  savingTermId,
  error,
  getPosition,
  getVariant,
  getStyleSetting,
  onChangeLayoutPosition,
  onChangeLayoutVariant,
  onChangeStyleSetting,
  onChangeStyleSettings,
  onResetStyleSettings,
}) {
  return (
    <PluginSidebar
      name="plugin-sidebar"
      title={__("Series Settings", "series-manager")}
      icon="admin-generic"
    >
      <PanelBody title={__("Series Settings", "series-manager")} initialOpen={true}>
        {isLoading && <Spinner />}

        {!isLoading && selectedSeries.length === 0 && (
          <p>{__("Select one or more series for this post first.", "series-manager")}</p>
        )}

        {error && (
          <Notice status="error" isDismissible={false}>
            {error}
          </Notice>
        )}

        {!isLoading &&
          selectedSeries.map((series) => (
            <div className="sm-series-layout-setting shadow-xl mb-6 p-3 border rounded" key={series.id}>
              <h4 className="sm-series-title font-bold mb-3">series: {series.name}</h4>
                <TabPanel
                className="sm-series-tabs"
                activeClass="is-active"
                tabs={[
                  {
                    name: "position",
                    title: __("Position", "series-manager"),
                    className: "sm-tab-position",
                  },
                  {
                    name: "layout",
                    title: __("Layout", "series-manager"),
                    className: "sm-tab-layout",
                  },
                  {
                    name: "style",
                    title: __("Style", "series-manager"),
                    className: "sm-tab-style",
                  },
                ]}
              >
                {(tab) => {
                  if (tab.name === "position") {
                    return (
                      <PositionTab
                        series={series}
                        getPosition={getPosition}
                        onChangeLayoutPosition={onChangeLayoutPosition}
                      />
                    );
                  }
                  if (tab.name === "layout") {
                    return (
                      <LayoutTab
                        series={series}
                        getVariant={getVariant}
                        onChangeLayoutVariant={onChangeLayoutVariant}
                      />
                    );
                  }
                  if (tab.name === "style") {
                    return (
                      <StyleTab
                        series={series}
                        savingTermId={savingTermId}
                        getStyleSetting={getStyleSetting}
                        onChangeStyleSetting={onChangeStyleSetting}
                        onChangeStyleSettings={onChangeStyleSettings}
                        onResetStyleSettings={onResetStyleSettings}
                      />
                    );
                  }
                  return null;
                }}
              </TabPanel>
              {savingTermId === series.id && (
                <div className="sm-series-layout-saving mt-2 text-sm text-gray-500">
                  <Spinner /> {__("Saving...", "series-manager")}
                </div>
              )}
            </div>

              // {savingTermId === series.id && (
              //   <span className="sm-series-layout-saving">
              //     {__("Saving...", "series-manager")}
              //   </span>
              // )}
          ))}
      </PanelBody>
    </PluginSidebar>
  );
}
