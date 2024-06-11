import { User } from '@prisma/client';

export type ResetPassword = Pick<User, 'password'>;
