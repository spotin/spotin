import { User } from '@/users/types/user';

export type ResetPasswordRequest = Pick<User, 'email'>;
