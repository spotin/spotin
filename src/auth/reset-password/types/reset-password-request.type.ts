import { User } from '@prisma/client';

export type ResetPasswordRequest = Pick<User, 'email'>;
