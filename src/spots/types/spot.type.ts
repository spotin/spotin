export type Spot = {
  uuid: string;
  title?: string;
  description?: string;
  latitude?: number;
  longitude?: number;
  timestamp?: string | Date | null;
  redirect?: string;
  referenced?: boolean;
  created_at: number;
  updated_at: number;
  deleted_at?: number;
};
