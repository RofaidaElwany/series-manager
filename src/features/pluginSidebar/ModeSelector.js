import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

const ModeSelector = ({ mode, onChange }) => {
  return (
    <SelectControl
      label={__("Mode", "series-manager")}
      value={mode}
      options={[
        { label: __("All Series", "series-manager"), value: "all" },
        { label: __("Top Series", "series-manager"), value: "top" },
        { label: __("User Series", "series-manager"), value: "user" },
        { label: __("Topics / CSCs", "series-manager"), value: "topics" },
      ]}
      onChange={onChange}
      __next40pxDefaultSize={true}
      __nextHasNoMarginBottom={true}
    />
  );
};

export default ModeSelector;
