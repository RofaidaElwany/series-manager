import ServerSideRender from "@wordpress/server-side-render";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { PanelBody, RangeControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import ModeSelector from "./components/ModeSelector";
import UserSelector from "./components/UserSelector";
import SeriesPreview from "./components/SeriesPreview";

const normalizeSeriesId = (value) => {
  if (value == null) {
    return null;
  }

  if (typeof value === "object") {
    const candidate = value.id ?? value.term_id ?? value;
    const num = Number(candidate);
    return Number.isNaN(num) ? null : num;
  }

  const num = Number(value);
  return Number.isNaN(num) ? null : num;
};

const Edit = ({ attributes, setAttributes }) => {
  const { mode, limit, userId } = attributes;
  const [previewRefreshKey, setPreviewRefreshKey] = useState(0);
  const blockProps = useBlockProps({
    className: "sm-series-editor-root",
  });

  const { postId, selectedSeriesIds } = useSelect((select) => {
    const editor = select("core/editor");
    const currentSeries = editor.getEditedPostAttribute("series") || [];

    return {
      postId: editor.getCurrentPostId(),
      selectedSeriesIds: currentSeries.map(normalizeSeriesId).filter(Boolean),
    };
  }, []);

  useEffect(() => {
    const refreshPreview = () => {
      setPreviewRefreshKey((key) => key + 1);
    };

    window.addEventListener("sm-series-preview-refresh", refreshPreview);

    return () => {
      window.removeEventListener("sm-series-preview-refresh", refreshPreview);
    };
  }, []);

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
      <div {...blockProps}>
        <ServerSideRender
          block="series-manager/series-list"
          attributes={attributes}
          urlQueryArgs={{
            post_id: postId,
            series_ids: selectedSeriesIds.join(","),
            preview_key: previewRefreshKey,
          }}
          EmptyResponsePlaceholder={() => (
            <p>{__("Select a series for this post to preview the layout.", "series-manager")}</p>
          )}
        />
      </div>
    </>
  );
};

export default Edit;
