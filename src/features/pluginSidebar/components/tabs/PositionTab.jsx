import { __ } from "@wordpress/i18n";
import { __experimentalToggleGroupControl as ToggleGroupControl, __experimentalToggleGroupControlOption as ToggleGroupControlOption } from "@wordpress/components";

export function PositionTab({ series, getPosition, onChangeLayoutPosition }) {
  return (
    <div className="sm-series-layout-setting shadow-xl">
      <ToggleGroupControl
        __next40pxDefaultSize={true}
        className="sm-position-toggle min-width-80px"
        label={__("Display position", "series-manager")}
        value={getPosition(series)}
        onChange={(value) => onChangeLayoutPosition(series.id, value)}
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
    </div>
  );
}