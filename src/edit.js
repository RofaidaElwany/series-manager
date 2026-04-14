import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import { ServerSideRender } from "@wordpress/server-side-render";
import {
  PanelBody,
  SelectControl,
  RangeControl,
  Spinner,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { __, sprintf, _n } from "@wordpress/i18n";

const Edit = ({ attributes, setAttributes }) => {
  const { mode, limit, userId } = attributes;

  // Fetch users for the user selector
  const users = useSelect((select) => {
    return select("core").getUsers({ per_page: -1 }) || [];
  }, []);

  // Fetch series terms for sidebar preview
  const { seriesTerms, isResolvingSeriesTerms } = useSelect(
    (select) => {
      const query = {
        per_page: mode === "top" ? limit : 100,
        hide_empty: mode === "top",
        orderby: mode === "top" ? "count" : "name",
        order: mode === "top" ? "desc" : "asc",
      };

      const terms =
        select("core").getEntityRecords("taxonomy", "series", query) || [];

      const isResolving = select("core").isResolving("getEntityRecords", [
        "taxonomy",
        "series",
        query,
      ]);

      // For user and topics modes, show limited preview since we can't easily filter client-side
      const previewTerms =
        (mode === "user" || mode === "topics") && terms.length > 0
          ? terms.slice(0, 5)
          : terms;

      return {
        seriesTerms: previewTerms,
        isResolvingSeriesTerms: isResolving,
      };
    },
    [mode, limit, userId],
  );

  const userOptions = [
    { label: __("Select a user", "series-manager"), value: "" },
    ...users.map((user) => ({
      label: user.name,
      value: user.id.toString(),
    })),
  ];

  return (
    <>
      <InspectorControls>
        <PanelBody title={__("Series Display Mode", "series-manager")}>
          <SelectControl
            label={__("Mode", "series-manager")}
            value={mode}
            options={[
              { label: __("All Series", "series-manager"), value: "all" },
              { label: __("Top Series", "series-manager"), value: "top" },
              { label: __("User Series", "series-manager"), value: "user" },
              { label: __("Topics / CSCs", "series-manager"), value: "topics" },
            ]}
            onChange={(value) => setAttributes({ mode: value })}
            __next40pxDefaultSize={true}
            __nextHasNoMarginBottom={true}
          />

          {mode === "top" && (
            <RangeControl
              label={__("Limit", "series-manager")}
              value={limit}
              onChange={(value) => setAttributes({ limit: value })}
              min={1}
              max={20}
              __nextHasNoMarginBottom={true}
            />
          )}

          {(mode === "user" || mode === "topics") && (
            <SelectControl
              label={__("Select User", "series-manager")}
              value={userId}
              options={userOptions}
              onChange={(value) => setAttributes({ userId: value })}
              __next40pxDefaultSize={true}
              __nextHasNoMarginBottom={true}
            />
          )}

          {/* Series Preview Section within the same panel */}
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
        </PanelBody>
      </InspectorControls>

      <div className="series-list-block-preview">
        <p>
          {__(
            "Series preview is available in the block settings panel on the right.",
            "series-manager",
          )}
        </p>
        <p>
          {__(
            "The frontend content will render when the post is published.",
            "series-manager",
          )}
        </p>
      </div>
    </>
  );
};

const Save = () => {
  return null; // Dynamic block
};

registerBlockType("series-manager/series-list", {
  title: __("Series List", "series-manager"),
  icon: "list-view",
  category: "widgets",
  attributes: {
    mode: {
      type: "string",
      default: "all",
    },
    limit: {
      type: "number",
      default: 5,
    },
    userId: {
      type: "string",
      default: "",
    },
  },
  edit: Edit,
  save: Save,
});
