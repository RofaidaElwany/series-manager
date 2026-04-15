import { Spinner } from "@wordpress/components";
import { __, sprintf, _n } from "@wordpress/i18n";

const SeriesPreview = ({ mode, seriesTerms, isResolvingSeriesTerms }) => {
  return (
    <div
      style={{
        marginTop: "16px",
        paddingTop: "16px",
        borderTop: "1px solid #ddd",
      }}
    >
      <label
        style={{
          display: "block",
          fontSize: "11px",
          fontWeight: "500",
          color: "#666",
          marginBottom: "8px",
          textTransform: "uppercase",
        }}
      >
        {__("Series Preview", "series-manager")}
      </label>

      <div className="series-sidebar-preview">
        {(mode === "user" || mode === "topics") && (
          <div className="series-mode-notice">
            <p className="series-notice-text">
              {mode === "user"
                ? __(
                    "Preview shows sample series. Actual results depend on selected user.",
                    "series-manager",
                  )
                : __(
                    "Topics taxonomy not available for preview.",
                    "series-manager",
                  )}
            </p>
          </div>
        )}

        {isResolvingSeriesTerms ? (
          <div className="series-loading">
            <Spinner />
            <span>{__("Loading series...", "series-manager")}</span>
          </div>
        ) : !seriesTerms || seriesTerms.length === 0 ? (
          <p className="series-empty">
            {mode === "topics"
              ? __("Topics taxonomy not available.", "series-manager")
              : __("No series found.", "series-manager")}
          </p>
        ) : (
          <div className="series-list">
            <p className="series-count">
              {mode === "top"
                ? sprintf(
                    _n(
                      "Top %d series by popularity",
                      "Top %d series by popularity",
                      seriesTerms.length,
                      "series-manager",
                    ),
                    seriesTerms.length,
                  )
                : sprintf(
                    _n(
                      "%d series",
                      "%d series",
                      seriesTerms.length,
                      "series-manager",
                    ),
                    seriesTerms.length,
                  )}
            </p>
            <ul className="series-items">
              {seriesTerms.slice(0, 10).map((term) => (
                <li key={term.id} className="series-item">
                  <span className="series-name">{term.name}</span>
                  <span className="series-count">({term.count})</span>
                </li>
              ))}
              {seriesTerms.length > 10 && (
                <li className="series-more">
                  {sprintf(
                    __("... and %d more", "series-manager"),
                    seriesTerms.length - 10,
                  )}
                </li>
              )}
            </ul>
          </div>
        )}
      </div>
    </div>
  );
};

export default SeriesPreview;
