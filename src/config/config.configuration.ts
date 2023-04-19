import {
  FQDN,
  JWT_SECRET,
  JWT_EXPIRATION_TIME,
  NODE_ENV,
} from '@/config/config.constants';

export const ConfigConfiguration = () => ({
  [FQDN]: process.env.SPOT_IN_FQDN as string,
  [JWT_SECRET]: process.env.SPOT_IN_JWT_SECRET as string,
  [JWT_EXPIRATION_TIME]: process.env.SPOT_IN_JWT_EXPIRATION_TIME as string,
  [NODE_ENV]: process.env.NODE_ENV as string,
});
