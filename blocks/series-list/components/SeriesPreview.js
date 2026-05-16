import { Spinner } from "@wordpress/components";
import { __, sprintf, _n } from "@wordpress/i18n";

const SeriesPreview = ({ mode, seriesTerms, isResolvingSeriesTerms }) => {
  return (
    <div className="mt-4 pt-4 border-t border-surface-container-high">

      {/* Header */}
      <div className="mb-3">
        <label className="text-xs font-semibold uppercase tracking-wide text-on-surface-variant">
          {__("Series Preview", "series-manager")}
        </label>
      </div>

      <div className="space-y-3">

        {/* MODE NOTICE */}
        {(mode === "user" || mode === "topics") && (
          <div className="p-3 rounded-lg bg-surface-container text-body-sm text-on-surface-variant border border-outline-variant">
            <p>
              {mode === "user"
                ? __(
                    "Preview shows sample series. Actual results depend on selected user.",
                    "series-manager"
                  )
                : __(
                    "Topics taxonomy not available for preview.",
                    "series-manager"
                  )}
            </p>
          </div>
        )}

        {/* LOADING */}
        {isResolvingSeriesTerms ? (
          <div className="flex items-center gap-2 p-3 rounded-lg bg-surface-container">
            <Spinner />
            <span className="text-body-sm text-on-surface-variant">
              {__("Loading series...", "series-manager")}
            </span>
          </div>
        ) : !seriesTerms || seriesTerms.length === 0 ? (
          <div className="p-3 rounded-lg bg-surface-container text-body-sm text-on-surface-variant">
            {mode === "topics"
              ? __("Topics taxonomy not available.", "series-manager")
              : __("No series found.", "series-manager")}
          </div>
        ) : (
          <div className="space-y-3">

            {/* COUNT HEADER */}
            <div className="flex items-center justify-between">
              <p className="text-sm font-medium text-on-surface">
                {mode === "top"
                  ? sprintf(
                      _n(
                        "Top %d series by popularity",
                        "Top %d series by popularity",
                        seriesTerms.length,
                        "series-manager"
                      ),
                      seriesTerms.length
                    )
                  : sprintf(
                      _n(
                        "%d series",
                        "%d series",
                        seriesTerms.length,
                        "series-manager"
                      ),
                      seriesTerms.length
                    )}
              </p>

              <span className="text-xs px-2 py-1 rounded-full bg-primary-container text-on-primary">
                {mode === "top" ? "Top" : seriesTerms.length}
              </span>
            </div>

            {/* LIST */}
            <div className="space-y-2">

              {seriesTerms.slice(0, 10).map((term, index) => (
                <div
                  key={term.id}
                  className="flex items-center justify-between p-3 rounded-lg border border-outline-variant bg-surface-container hover:bg-surface-container-high transition"
                >
                  {/* NAME */}
                  <span className="text-sm font-medium text-on-surface">
                    {term.name}
                  </span>

                  {/* COUNT BADGE */}
                  <span className="text-xs px-2 py-1 rounded-md bg-surface-container-high text-on-surface-variant">
                    {term.count}
                  </span>
                </div>
              ))}

              {/* MORE */}
              {seriesTerms.length > 10 && (
                <div className="text-xs text-on-surface-variant px-2">
                  {sprintf(
                    __("... and %d more", "series-manager"),
                    seriesTerms.length - 10
                  )}
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default SeriesPreview;