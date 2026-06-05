import { PluginSidebar } from "@wordpress/editor";
import { Notice, PanelBody, Spinner, __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,} from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";
import {LayoutVariantSelector } from "./components/LayoutVariantSelector";
import "../../index.css";
export function SidebarView({
  selectedSeries,
  isLoading,
  savingTermId,
  error,
  getPosition,
  getVariant,
  onChangeLayoutPosition,
  onChangeLayoutVariant,
}) {
  return (
    <PluginSidebar
      name="plugin-sidebar"
      title={__("Series Settings", "series-manager")}
      icon="admin-generic"
    >
      <PanelBody title={__("Layout Position", "series-manager")} initialOpen={true}>
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
            <div className="sm-series-layout-setting shadow-xl p-4 border border-radius-12px" key={series.id}>

              <ToggleGroupControl
                className="sm-position-toggle min-w-full"
                label={__("Display position ", "series-manager")}
                value={getPosition(series)}
                onChange={(value) =>
                  onChangeLayoutPosition(series.id, value)
                }
              >
                <ToggleGroupControlOption
                  value="top"
                  label={__("↑ Top", "series-manager")}
                />
                <ToggleGroupControlOption
                  value="bottom"
                  label={__("↓ Bottom", "series-manager")}
                />
              </ToggleGroupControl>
                
                <LayoutVariantSelector
                  label={sprintf(
                    __("Display variant for %s", "series-manager"),
                    series.name,
                  )}
                  value={getVariant(series)}
                  onChange={(layout) =>
                    onChangeLayoutVariant(series.id, layout)
                  }
                />

              {savingTermId === series.id && (
                <span className="sm-series-layout-saving">
                  {__("Saving...", "series-manager")}
                </span>
              )}
            </div>
          ))}
      </PanelBody>
    </PluginSidebar>
  );
}
