import { User } from '@prisma/client';

export type SignupUser = Pick<User, 'username' | 'email' | 'password'>;
