import { 
        __experimentalPanelColorGradientSettings as PanelColorGradientSettings, 
        __experimentalSpacingSizesControl as SpacingSizesControl
        } from "@wordpress/block-editor";
import {
        PanelBody,
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
      <PanelBody
          title={__("Border", "series-manager")}
          initialOpen={false}
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
      </PanelBody>
      <div className="sm-series-dimensions"> 
          <PanelBody 
              className="sm-series-dimensions-panel"
              title={__("Dimensions", "series-manager")}
              initialOpen={false}
          >
            <SpacingSizesControl
            className="sm-series-dimensions-spacing"
            label={__("Padding", "series-manager")}
            values={padding}
            onChange={(next) =>
              onChangeStyleSettings(series.id, { padding: next })
            }
          />

            <SpacingSizesControl
            className="sm-series-dimensions-spacing"
            label={__("Margin", "series-manager")}
            values={margin}
            onChange={(next) =>
              onChangeStyleSettings(series.id, { margin: next })
            }
          />
          </PanelBody>
      </div>  
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
