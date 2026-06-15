import { __experimentalPanelColorGradientSettings as PanelColorGradientSettings } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { DimensionSettings } from "./DimensionSettings";
import { hasCustomStyles } from "../utils/styleSettings";

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
