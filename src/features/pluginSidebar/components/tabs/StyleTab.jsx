import { 
        __experimentalPanelColorGradientSettings as PanelColorGradientSettings, 
        __experimentalSpacingSizesControl as SpacingSizesControl
        } from "@wordpress/block-editor";
import {
        Button,
        __experimentalBorderControl as BorderControl,
        __experimentalToolsPanel as ToolsPanel,
        __experimentalToolsPanelItem as ToolsPanelItem, 
        } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { useInstanceId } from "@wordpress/compose";
import { useSelect } from "@wordpress/data";
import {
  DEFAULT_SPACING,
  hasBorderValue,
  hasSpacingValue,
  hasCustomStyles,
} from "../utils/styleSettings";


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
  const toolsPanelDropdownMenuProps = {
    popoverProps: {
      placement: "left-start",
    },
  };
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
        dropdownMenuProps={toolsPanelDropdownMenuProps}
      >
        <ToolsPanelItem
          className="block-editor-tools-panel-color-gradient-settings__item"
          hasValue={() => hasSpacingValue(padding)}
          label={__("Padding", "series-manager")}
          onDeselect={() =>
            onChangeStyleSettings(series.id, { padding: undefined })
          }
          isShownByDefault={false}
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
          isShownByDefault={false}
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
        dropdownMenuProps={toolsPanelDropdownMenuProps}
      >
        <ToolsPanelItem
          className="block-editor-tools-panel-color-gradient-settings__item"
          hasValue={() => hasBorderValue(border)}
          label={__("Border", "series-manager")}
          onDeselect={() =>
            onChangeStyleSettings(series.id, { border: undefined })
          }
          isShownByDefault={false}
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


export function StyleTab({
  series,
  savingTermId,
  getStyleSetting,
  onChangeStyleSetting,
  onChangeStyleSettings,
  onResetStyleSettings,
}) {
  const isSaving = savingTermId === series.id;

  return (
    <div className="sm-tab-content-style">
      <PanelColorGradientSettings
        title={__("Color", "series-manager")}
        settings={[
          {
            label: __("Title", "series-manager"),
            colorValue: getStyleSetting(series, "titleColor"),
            onColorChange: (color) =>
              onChangeStyleSetting(series.id, "titleColor", color || ""),
          },
          {
            label: __("Header Background", "series-manager"),
            colorValue: getStyleSetting(series, "headerBackgroundColor"),
            onColorChange: (color) =>
              onChangeStyleSetting(
                series.id,
                "headerBackgroundColor",
                color || "",
              ),
          },
          {
            label: __("Button", "series-manager"),
            colorValue: getStyleSetting(series, "buttonColor"),
            onColorChange: (color) =>
              onChangeStyleSetting(series.id, "buttonColor", color || ""),
          },
        ]}
        disableCustomGradients
        __experimentalIsRenderedInSidebar
      />

      

      <DimensionSettings
        series={series}
        getStyleSetting={getStyleSetting}
        onChangeStyleSettings={onChangeStyleSettings}
      />

      <Button
        className="sm-style-reset-button"
        variant="secondary"
        onClick={() => onResetStyleSettings(series.id)}
        disabled={!hasCustomStyles(series, getStyleSetting) || isSaving}
        isBusy={isSaving}
      >
        {__("Reset to default styles", "series-manager")}
      </Button>
    </div>
  );
}
