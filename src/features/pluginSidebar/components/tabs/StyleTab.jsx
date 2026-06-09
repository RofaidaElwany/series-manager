import { __ } from "@wordpress/i18n";
import { ColorPalette, __experimentalUnitControl as UnitControl, PanelRow } from "@wordpress/components";

export function StyleTab({ series, getStyleSetting, onChangeStyleSetting }) {
  // get current style settings for the series
  const primaryColor = getStyleSetting(series, 'primaryColor') || '#007cba';
  const itemPadding = getStyleSetting(series, 'padding') || '10px';

  return (
    <div className="sm-tab-content-style mt-4">
      {/* Color Control */}
      <div className="sm-style-control-group mb-4">
        <p className="sm-style-label font-bold mb-2">{__("Primary Color", "series-manager")}</p>
        <ColorPalette
          value={primaryColor}
          onChange={(color) => onChangeStyleSetting(series.id, 'primaryColor', color)}
        />
      </div>

      <hr className="my-4" />

      {/* Unit Control */}
      <div className="sm-style-control-group mb-4">
        <PanelRow>
          <UnitControl
            label={__("Item Padding", "series-manager")}
            value={itemPadding}
            onChange={(padding) => onChangeStyleSetting(series.id, 'padding', padding)}
          />
        </PanelRow>
      </div>
    </div>
  );
}