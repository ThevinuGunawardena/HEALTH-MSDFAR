/**
 * Formats a Date object to a local ISO string (YYYY-MM-DDTHH:mm:ss) 
 * without UTC conversion (no 'Z' suffix).
 * This ensures the date and time selected by the user in their local timezone 
 * is preserved when sent to the server.
 */
export function toLocalISOString(date: Date | string | null | undefined): string | null {
  if (!date) return null;
  const d = new Date(date);
  if (isNaN(d.getTime())) return null;

  const pad = (num: number) => String(num).padStart(2, '0');

  const year = d.getFullYear();
  const month = pad(d.getMonth() + 1);
  const day = pad(d.getDate());
  const hours = pad(d.getHours());
  const minutes = pad(d.getMinutes());
  const seconds = pad(d.getSeconds());

  return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
}
