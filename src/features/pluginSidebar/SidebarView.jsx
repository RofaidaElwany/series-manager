import { PluginSidebar } from "@wordpress/editor";
import { Notice, PanelBody, RadioControl, SelectControl, Spinner } from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";

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
            <div className="sm-series-layout-setting" key={series.id}>
              <RadioControl
                label={sprintf(
                  __("Display position for %s", "series-manager"),
                  series.name,
                )}
                selected={getPosition(series)}
                options={[
                  { label: __("Top of post", "series-manager"), value: "top" },
                  { label: __("Bottom of post", "series-manager"), value: "bottom" },
                ]}
                onChange={(position) =>
                  onChangeLayoutPosition(series.id, position)
                }
              />

              <SelectControl
                label={sprintf(
                  __("Display variant for %s", "series-manager"),
                  series.name,
                )}
                value={getVariant(series)}
                options={[
                  { label: __("Link list", "series-manager"), value: "link-list" },
                  { label: __("Media list", "series-manager"), value: "media-list" },
                  { label: __("Link grid", "series-manager"), value: "link-grid" },
                  { label: __("Media grid", "series-manager"), value: "media-grid" },
                ]}
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
