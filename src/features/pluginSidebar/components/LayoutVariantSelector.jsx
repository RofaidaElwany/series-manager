import { __ } from "@wordpress/i18n";
import { LayoutPreview } from "./LayoutPreview";
import "../../../index.css";


const LAYOUT_OPTIONS = [
  {
    value: "media-grid",
    label: __("Media Grid", "series-manager"),
  },
  {
    value: "link-grid",
    label: __("Link Grid", "series-manager"),
  },
  {
    value: "media-list",
    label: __("Media List", "series-manager"),
  },
  {
    value: "link-list",
    label: __("Link List", "series-manager"),
  },
];

export function LayoutVariantSelector({
  value,
  onChange,
  label,
}) {
  return (
    <div className="sm-layout-selector mb-4 mt-4">
      {label && (
        <p className="sm-layout-selector__label">
          {label}
        </p>
      )}

      <div className="sm-layout-selector__grid">
        {LAYOUT_OPTIONS.map((option) => (
          <button
            key={option.value}
            type="button"
            className={`sm-layout-card ${
              value === option.value ? "is-selected" : ""
            }`}
            aria-pressed={value === option.value}
            onClick={() => onChange(option.value)}
          >
            <span className="sm-layout-card__indicator" />
            <LayoutPreview type={option.value} />
            <div className="sm-layout-card__content">
              <div className="sm-layout-card__title">
                {option.label}
              </div>
            </div>
          </button>
        ))}
      </div>
    </div>
  );
}
