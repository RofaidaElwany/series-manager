import { Spinner } from "@wordpress/components";
import { __, sprintf, _n } from "@wordpress/i18n";

const SeriesPreview = ({ mode, seriesTerms, isResolvingSeriesTerms }) => {
  return (
    <div className="bg-white border border-gray-100 rounded-lg shadow-sm">

      {/* Header */}
      <div className="flex items-center justify-between px-3 py-3 border-b border-gray-50">
        <label className="text-[10px] font-bold text-blue-600 uppercase tracking-wider">
          {__("Serieses", "series-manager")}
        </label>
         <p className="text-[10px] px-2 py-0.5 bg-blue-50 text-blue-600 rounded-full font-medium">
                {mode === "top"
                  ? sprintf(
                      _n(
                        "Top %d series ",
                        "Top %d series ",
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
            {/* LIST */}
            <div className=" series-scrollbar divide-y divide-gray-50 max-h-64 overflow-y-auto pr-1">

              {seriesTerms.map((term, index) => (
                <div
                  key={term.id}
                  className="flex items-center px-3 py-2 text-sm"
                >
                  {/* NAME */}
                  <span className="flex-1 text-gray-700 font-medium text-x1">
                    {term.name}
                  </span>

                  {/* COUNT BADGE */}
                  <span className="text-xs px-2 py-1 text-on-surface-variant">
                    ({term.count})
                  </span>
                </div>
              ))}

              {/* MORE */}
              {/* {seriesTerms.length > 10 && (
                <div className="text-xs text-on-surface-variant px-2">
                  {sprintf(
                    __("... and %d more", "series-manager"),
                    seriesTerms.length - 10
                  )}
                </div>
              )} */}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default SeriesPreview;