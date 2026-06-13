import { __experimentalSpacingSizesControl as SpacingSizesControl } from "@wordpress/block-editor";
import {
  __experimentalBorderControl as BorderControl,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
} from "@wordpress/components";
import { useInstanceId } from "@wordpress/compose";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";
import {
  DEFAULT_SPACING,
  hasBorderValue,
  hasSpacingValue,
} from "./styleSettings";

export function DimensionSettings({
  series,
  getStyleSetting,
  onChangeStyleSettings,
}) {
  const dimensionsPanelId = useInstanceId(
    DimensionSettings,
    "sm-series-dimensions",
  );
  const borderPanelId = useInstanceId(DimensionSettings, "sm-series-border");

  const colors = useSelect(
    (select) => select("core/block-editor").getSettings()?.colors ?? [],
    [],
  );

  const padding = getStyleSetting(series, "padding") || DEFAULT_SPACING;
  const margin = getStyleSetting(series, "margin") || DEFAULT_SPACING;
  const border = getStyleSetting(series, "border");

  return (
    <>
      <ToolsPanel
        className="sm-series-dimensions-panel"
        label={__("Dimensions", "series-manager")}
        panelId={dimensionsPanelId}
        resetAll={() =>
          onChangeStyleSettings(series.id, {
            padding: undefined,
            margin: undefined,
          })
        }
      >
        <ToolsPanelItem
          className="block-editor-tools-panel-color-gradient-settings__item"
          hasValue={() => hasSpacingValue(padding)}
          label={__("Padding", "series-manager")}
          onDeselect={() =>
            onChangeStyleSettings(series.id, { padding: undefined })
          }
          isShownByDefault={true}
          panelId={dimensionsPanelId}
        >
          <SpacingSizesControl
            label={__("Padding", "series-manager")}
            values={padding}
            onChange={(next) =>
              onChangeStyleSettings(series.id, { padding: next })
            }
          />
        </ToolsPanelItem>

        <ToolsPanelItem
          className="block-editor-tools-panel-color-gradient-settings__item"
          hasValue={() => hasSpacingValue(margin)}
          label={__("Margin", "series-manager")}
          onDeselect={() =>
            onChangeStyleSettings(series.id, { margin: undefined })
          }
          isShownByDefault={true}
          panelId={dimensionsPanelId}
        >
          <SpacingSizesControl
            label={__("Margin", "series-manager")}
            values={margin}
            onChange={(next) =>
              onChangeStyleSettings(series.id, { margin: next })
            }
          />
        </ToolsPanelItem>
      </ToolsPanel>

      <ToolsPanel
        className="sm-series-border-panel"
        label={__("Border", "series-manager")}
        panelId={borderPanelId}
        resetAll={() =>
          onChangeStyleSettings(series.id, { border: undefined })
        }
      >
        <ToolsPanelItem
          className="block-editor-tools-panel-color-gradient-settings__item"
          hasValue={() => hasBorderValue(border)}
          label={__("Border", "series-manager")}
          onDeselect={() =>
            onChangeStyleSettings(series.id, { border: undefined })
          }
          isShownByDefault={true}
          panelId={borderPanelId}
        >
          <BorderControl
            __next40pxDefaultSize={true}
            colors={colors}
            label={__("Border", "series-manager")}
            onChange={(next) =>
              onChangeStyleSettings(series.id, { border: next })
            }
            value={border}
            withSlider={true}
            __experimentalIsRenderedInSidebar={true}
          />
        </ToolsPanelItem>
      </ToolsPanel>
    </>
  );
}
