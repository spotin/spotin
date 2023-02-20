export type Spot = {
  uuid: string;
  title?: string;
  description?: string;
  timestamp?: string | Date | null;
  redirection?: string;
  referenced?: boolean;
  created_at: number;
  updated_at: number;
  deleted_at?: number;
};
