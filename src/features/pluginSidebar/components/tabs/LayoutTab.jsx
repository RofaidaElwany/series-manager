import { __, sprintf } from "@wordpress/i18n";
import { LayoutVariantSelector } from "../LayoutVariantSelector";

export function LayoutTab({ series, getVariant, onChangeLayoutVariant }) {
  return (
    <div className="sm-tab-content-layout">
      <LayoutVariantSelector
        label={sprintf(
          __("Display variant for %s", "series-manager"),
          series.name
        )}
        value={getVariant(series)}
        onChange={(layout) => onChangeLayoutVariant(series.id, layout)}
      />
    </div>
  );
}