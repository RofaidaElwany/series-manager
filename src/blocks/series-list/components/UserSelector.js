import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

const UserSelector = ({ userId, users, onChange }) => {
  const userOptions = [
    { label: __("Select a user", "series-manager"), value: "" },
    ...users.map((user) => ({
      label: user.name,
      value: user.id.toString(),
    })),
  ];

  return (
    <SelectControl
      label={__("Select User", "series-manager")}
      value={userId}
      options={userOptions}
      onChange={onChange}
      __next40pxDefaultSize={true}
      __nextHasNoMarginBottom={true}
    />
  );
};

export default UserSelector;
