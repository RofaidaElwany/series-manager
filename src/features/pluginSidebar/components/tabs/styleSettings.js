export const COLOR_STYLE_KEYS = [
  "titleColor",
  "headerBackgroundColor",
  "buttonColor",
];

export const OBJECT_STYLE_KEYS = ["padding", "margin", "border"];

export const DEFAULT_SPACING = {
  top: undefined,
  right: undefined,
  bottom: undefined,
  left: undefined,
};

export const hasSpacingValue = (values) => {
  if (!values || typeof values !== "object") {
    return false;
  }

  return Object.values(values).some(
    (value) => value !== undefined && value !== null && value !== "",
  );
};

export const hasBorderValue = (border) => {
  if (!border || typeof border !== "object") {
    return false;
  }

  return !!(border.width || border.color || border.style);
};

export const hasCustomStyles = (series, getStyleSetting) =>
  COLOR_STYLE_KEYS.some((key) => getStyleSetting(series, key)) ||
  hasSpacingValue(getStyleSetting(series, "padding")) ||
  hasSpacingValue(getStyleSetting(series, "margin")) ||
  hasBorderValue(getStyleSetting(series, "border"));

export const normalizeStyleUpdates = (updates) => {
  const normalized = { ...updates };

  ["padding", "margin"].forEach((key) => {
    if (!normalized[key] || typeof normalized[key] !== "object") {
      return;
    }

    const cleaned = {};

    Object.entries(normalized[key]).forEach(([side, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        cleaned[side] = value;
      }
    });

    normalized[key] = Object.keys(cleaned).length > 0 ? cleaned : undefined;
  });

  if (normalized.border !== undefined && !hasBorderValue(normalized.border)) {
    normalized.border = undefined;
  }

  Object.entries(normalized).forEach(([key, value]) => {
    if (value === undefined) {
      delete normalized[key];
    }
  });

  return normalized;
};
