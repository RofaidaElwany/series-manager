import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, RangeControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";
import ModeSelector from "./components/ModeSelector";
import UserSelector from "./components/UserSelector";
import SeriesPreview from "./components/SeriesPreview";

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

  return (
    <>
      <InspectorControls>
        <PanelBody title={__("Series Display Mode", "series-manager")}>
          <ModeSelector
            mode={mode}
            onChange={(value) => setAttributes({ mode: value })}
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
            <UserSelector
              userId={userId}
              users={users}
              onChange={(value) => setAttributes({ userId: value })}
            />
          )}

          <SeriesPreview
            mode={mode}
            seriesTerms={seriesTerms}
            isResolvingSeriesTerms={isResolvingSeriesTerms}
          />
        </PanelBody>
      </InspectorControls>

    </>
  );
};

export default Edit;
